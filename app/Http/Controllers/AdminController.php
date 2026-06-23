<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalParticipants = \App\Models\Participant::count();
        $totalRevenue = \App\Models\Payment::where('payment_status', 'paid')->sum('amount');
        $ticketsSold = \App\Models\Ticket::where('status', 'valid')->count();
        $checkedIn = \App\Models\Ticket::where('status', 'checked-in')->count();

        return view('admin.dashboard', compact('totalParticipants', 'totalRevenue', 'ticketsSold', 'checkedIn'));
    }

    public function participants(Request $request)
    {
        $query = \App\Models\Participant::with(['ticket.payments', 'user'])->latest();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $participants = $query->get();
        $currentCategory = $request->category ?? 'all';

        return view('admin.participants', compact('participants', 'currentCategory'));
    }

    public function editParticipant($id)
    {
        $participant = \App\Models\Participant::findOrFail($id);
        return view('admin.participant-edit', compact('participant'));
    }

    public function updateParticipant(Request $request, $id)
    {
        $participant = \App\Models\Participant::findOrFail($id);
        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'category' => 'required|in:3K,5K,10K',
            'jersey_size' => 'required|in:S,M,L,XL,XXL',
        ]);

        $participant->update($request->only('fullname', 'phone', 'category', 'jersey_size'));

        return redirect()->route('admin.participants')->with('success', 'Participant data updated successfully!');
    }

    public function deleteParticipant($id)
    {
        $participant = \App\Models\Participant::findOrFail($id);
        
        // Manual cascade delete
        if ($participant->ticket) {
            $participant->ticket->payments()->delete();
            $participant->ticket()->delete();
        }
        $participant->delete();

        return redirect()->back()->with('success', 'Participant deleted successfully!');
    }

    public function scanner()
    {
        return view('admin.scanner');
    }

    public function scanTicket(Request $request)
    {
        $code = $request->qr_code;
        
        // Cek berdasarkan qr_code atau ticket_code (untuk manual entry)
        $ticket = \App\Models\Ticket::where('qr_code', $code)
                                    ->orWhere('ticket_code', $code)
                                    ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket not found!']);
        }

        if ($ticket->status === 'checked-in') {
            return response()->json(['success' => false, 'message' => 'Ticket already checked in!']);
        }

        if ($ticket->status !== 'valid') {
            return response()->json(['success' => false, 'message' => 'Ticket is not valid or payment pending.']);
        }

        $ticket->update(['status' => 'checked-in']);

        return response()->json([
            'success' => true, 
            'message' => 'Check-in successful!',
            'participant' => $ticket->participant->fullname
        ]);
    }

    public function exportCSV()
    {
        $participants = \App\Models\Participant::with(['user', 'ticket.payments'])->get();
        
        $filename = "participants_funrun_2026.csv";
        $handle = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($handle, ['ID', 'Full Name', 'Email', 'Phone', 'Category', 'Jersey Size', 'Ticket Code', 'Payment Status', 'Check-in Status']);

        foreach ($participants as $p) {
            $paymentStatus = 'Pending';
            if ($p->ticket && $p->ticket->payments->count() > 0) {
                $paymentStatus = $p->ticket->payments->first()->payment_status;
            }

            fputcsv($handle, [
                $p->id,
                $p->fullname,
                $p->user->email ?? '-',
                $p->phone,
                $p->category,
                $p->jersey_size,
                $p->ticket->ticket_code ?? '-',
                $paymentStatus,
                $p->ticket->status ?? 'pending'
            ]);
        }

        fclose($handle);

        return response()->stream(function() use ($handle) {}, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function payments(Request $request)
    {
        $query = \App\Models\Payment::with(['ticket.participant'])->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        $payments = $query->get();
        $currentStatus = $request->status ?? 'all';

        return view('admin.payments', compact('payments', 'currentStatus'));
    }

    public function approvePayment($id)
    {
        $payment = \App\Models\Payment::findOrFail($id);
        
        $payment->update(['payment_status' => 'paid']);
        
        if ($payment->ticket) {
            $payment->ticket->update([
                'status' => 'valid',
                'qr_code' => 'QR-' . $payment->ticket->ticket_code . '-' . uniqid()
            ]);

            $participant = $payment->ticket->participant;
            $ticketUrl = route('ticket.show', $payment->ticket->ticket_code);
            $waMessage = "🎉 *PEMBAYARAN BERHASIL* 🎉\n\nHalo *{$participant->fullname}*,\n\nPembayaran Anda untuk event FunRun 2026 telah berhasil diverifikasi!\n\n*Detail Tiket:*\nKode: {$payment->ticket->ticket_code}\nKategori: {$participant->category}\nJersey: {$participant->jersey_size}\n\nSilakan klik link di bawah ini untuk melihat E-Ticket dan QR Code Anda:\n{$ticketUrl}\n\nTerima kasih dan sampai jumpa di garis start!";

            // Attempt to send via Fonnte WA API (if configured in .env)
            $fonnteToken = env('FONNTE_TOKEN');
            if ($fonnteToken) {
                try {
                    \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => $fonnteToken,
                    ])->post('https://api.fonnte.com/send', [
                        'target' => $participant->phone,
                        'message' => $waMessage,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('WA API Error: ' . $e->getMessage());
                }
            } else {
                // Log the message if API is not configured
                \Illuminate\Support\Facades\Log::info("WA Message to {$participant->phone}: \n" . $waMessage);
            }
        }

        return redirect()->back()->with('success', 'Payment approved! Ticket validated and WA notification queued.');
    }
}
