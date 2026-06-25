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

        $currentCategory = $request->category ?? 'all';
        if ($currentCategory !== 'all') {
            $query->where('category', $currentCategory);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ticket', function($qt) use ($search) {
                      $qt->where('ticket_code', 'like', "%{$search}%");
                  });
            });
        }

        $participants = $query->paginate(10)->withQueryString();

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

    public function bulkDestroyParticipant(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Select participants to delete first.');
        }

        foreach ($ids as $id) {
            $participant = \App\Models\Participant::find($id);
            if ($participant) {
                if ($participant->ticket) {
                    $participant->ticket->payments()->delete();
                    $participant->ticket()->delete();
                }
                $participant->delete();
            }
        }

        return redirect()->back()->with('success', 'Selected participants deleted successfully!');
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

    public function exportCSV(Request $request)
    {
        $query = \App\Models\Participant::with(['user', 'ticket.payments'])->latest();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ticket', function($qt) use ($search) {
                      $qt->where('ticket_code', 'like', "%{$search}%");
                  });
            });
        }

        $participants = $query->get();
        
        $filename = "participants_funrun_2026.csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($participants) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write column headers
            fputcsv($file, ['ID', 'Nama Lengkap', 'Email', 'No. WhatsApp', 'Kategori', 'Ukuran Jersey', 'Kode Tiket', 'Status Pembayaran', 'Status Check-in'], ';');

            foreach ($participants as $p) {
                $paymentStatus = 'Pending';
                if ($p->ticket && $p->ticket->payments->count() > 0) {
                    $paymentStatus = $p->ticket->payments->first()->payment_status;
                }

                fputcsv($file, [
                    $p->id,
                    $p->fullname,
                    $p->user->email ?? '-',
                    $p->phone,
                    $p->category,
                    $p->jersey_size,
                    $p->ticket->ticket_code ?? '-',
                    strtoupper($paymentStatus),
                    strtoupper($p->ticket->status ?? 'pending')
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function payments(Request $request)
    {
        $query = \App\Models\Payment::with(['ticket.participant'])->latest();

        $currentStatus = $request->status ?? 'all';
        if ($currentStatus !== 'all') {
            $query->where('payment_status', $currentStatus);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_method', 'like', "%{$search}%")
                  ->orWhere('payment_status', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhereHas('ticket', function($qt) use ($search) {
                      $qt->where('ticket_code', 'like', "%{$search}%")
                        ->orWhereHas('participant', function($qp) use ($search) {
                            $qp->where('fullname', 'like', "%{$search}%")
                              ->orWhere('phone', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $payments = $query->paginate(10)->withQueryString();

        return view('admin.payments', compact('payments', 'currentStatus'));
    }

    public function bulkDestroyPayment(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Select payments to delete first.');
        }

        foreach ($ids as $id) {
            $payment = \App\Models\Payment::find($id);
            if ($payment) {
                if ($payment->ticket) {
                    $payment->ticket->update(['status' => 'pending']);
                }
                $payment->delete();
            }
        }

        return redirect()->back()->with('success', 'Selected payments deleted successfully!');
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
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('WA API Error: ' . $e->getMessage());
                }
            } else {
                // Log the message if API is not configured
                \Illuminate\Support\Facades\Log::info("WA Message to {$participant->phone}: \n" . $waMessage);
            }
        }

        return redirect()->back()->with('success', 'Payment approved! Ticket validated and WA notification queued.');
    }

    // ===== EVENT MANAGEMENT =====

    public function events()
    {
        $events = \App\Http\Controllers\HomeController::loadEvents();
        return view('admin.events', compact('events'));
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'lokasi'           => 'required|string|max:255',
            'tanggal'          => 'required|string|max:100',
            'harga'            => 'required|integer|min:0',
            'kategori'         => 'required|in:upcoming,highlight',
            'urlBeli'          => 'nullable|string|max:500',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,webp|max:1024',
            'waktu'            => 'nullable|string|max:100',
            'deskripsi'        => 'nullable|string|max:5000',
            'syarat_ketentuan' => 'nullable|string|max:5000',
        ]);

        $events = \App\Http\Controllers\HomeController::loadEvents();
        $maxId  = count($events) > 0 ? max(array_column($events, 'id')) : 0;

        $thumbnailPath = '';
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $thumbnailPath = '/storage/' . $path;
        }

        $events[] = [
            'id'               => $maxId + 1,
            'nama'             => $request->nama,
            'lokasi'           => $request->lokasi,
            'tanggal'          => $request->tanggal,
            'harga'            => (int) $request->harga,
            'thumbnail'        => $thumbnailPath,
            'kategori'         => $request->kategori,
            'urlBeli'          => $request->urlBeli ?: 'https://wa.me/6281393564042',
            'waktu'            => $request->waktu ?? '',
            'deskripsi'        => $request->deskripsi ?? '',
            'syarat_ketentuan' => $request->syarat_ketentuan ?? '',
        ];

        \App\Http\Controllers\HomeController::saveEvents($events);
        return redirect()->route('admin.events')->with('success', 'Event berhasil ditambahkan!');
    }

    public function updateEvent(Request $request, $id)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'lokasi'           => 'required|string|max:255',
            'tanggal'          => 'required|string|max:100',
            'harga'            => 'required|integer|min:0',
            'kategori'         => 'required|in:upcoming,highlight',
            'urlBeli'          => 'nullable|string|max:500',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,webp|max:1024',
            'waktu'            => 'nullable|string|max:100',
            'deskripsi'        => 'nullable|string|max:5000',
            'syarat_ketentuan' => 'nullable|string|max:5000',
        ]);

        $events = \App\Http\Controllers\HomeController::loadEvents();
        foreach ($events as &$ev) {
            if ($ev['id'] == $id) {
                $ev['nama']             = $request->nama;
                $ev['lokasi']           = $request->lokasi;
                $ev['tanggal']          = $request->tanggal;
                $ev['harga']            = (int) $request->harga;
                $ev['kategori']         = $request->kategori;
                $ev['urlBeli']          = $request->urlBeli ?: 'https://wa.me/6281393564042';
                $ev['waktu']            = $request->waktu ?? '';
                $ev['deskripsi']        = $request->deskripsi ?? '';
                $ev['syarat_ketentuan'] = $request->syarat_ketentuan ?? '';
                
                if ($request->hasFile('thumbnail')) {
                    $path = $request->file('thumbnail')->store('thumbnails', 'public');
                    $ev['thumbnail'] = '/storage/' . $path;
                }
                break;
            }
        }
        unset($ev);

        \App\Http\Controllers\HomeController::saveEvents($events);
        return redirect()->route('admin.events')->with('success', 'Event berhasil diperbarui!');
    }

    public function destroyEvent($id)
    {
        $events = \App\Http\Controllers\HomeController::loadEvents();
        $events = array_values(array_filter($events, fn($e) => $e['id'] != $id));
        \App\Http\Controllers\HomeController::saveEvents($events);
        return redirect()->route('admin.events')->with('success', 'Event berhasil dihapus!');
    }

    public function bulkDestroyEvent(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih event yang ingin dihapus terlebih dahulu.');
        }

        $events = \App\Http\Controllers\HomeController::loadEvents();
        $events = array_values(array_filter($events, fn($e) => !in_array($e['id'], $ids)));
        \App\Http\Controllers\HomeController::saveEvents($events);

        return redirect()->route('admin.events')->with('success', 'Event terpilih berhasil dihapus!');
    }
}
