<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $eventId = $request->query('event_id', 1);
        $events = \App\Http\Controllers\HomeController::loadEvents();
        $event = collect($events)->firstWhere('id', (int)$eventId);

        if (!$event) {
            // Fallback to first event or default
            $event = count($events) > 0 ? $events[0] : [
                'id' => 1,
                'nama' => 'FunRun 2026',
                'lokasi' => 'City Square',
                'tanggal' => '25-26 Juli 2026',
                'harga' => 100000,
                'kategori' => 'upcoming',
                'waktu' => '16.00 - 23.00'
            ];
        }

        // Apply fallbacks
        if (empty($event['waktu'])) {
            $event['waktu'] = '16.00 - 23.00';
        }

        return view('register', compact('event'));
    }

    public function submitRegistration(Request $request)
    {
        $validated = $request->validate([
            'fullname'          => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'phone'             => 'required|string|max:20',
            'dob'               => 'required|date',
            'gender'            => 'required|in:male,female',
            'address'           => 'required|string',
            'jersey_size'       => 'required|in:S,M,L,XL,XXL',
            'emergency_contact' => 'required|string|max:255',
            'category'          => 'required|in:3K,5K,10K',
            'event_id'          => 'required|integer',
            'payment_method'    => 'required|in:bank_transfer,ewallet',
            'proof'             => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 1. Create or Find User
        $user = \App\Models\User::firstOrCreate(
            ['email' => $validated['email']],
            ['name' => $validated['fullname'], 'password' => bcrypt('password'), 'role' => 'participant']
        );

        // 2. Find or Create Event in Database
        $eventId = $validated['event_id'];
        $events = \App\Http\Controllers\HomeController::loadEvents();
        $jsonEvent = collect($events)->firstWhere('id', (int)$eventId);

        $title = $jsonEvent['nama'] ?? 'FunRun 2026';
        
        // Parse a database-friendly date format
        $date = '2026-09-15';

        $event = \App\Models\Event::firstOrCreate(
            ['id' => $eventId],
            ['title' => $title, 'date' => $date, 'location' => $jsonEvent['lokasi'] ?? 'City Square', 'quota' => 5000]
        );

        // 3. Create Participant
        $participant = \App\Models\Participant::create([
            'user_id'           => $user->id,
            'event_id'          => $event->id,
            'fullname'          => $validated['fullname'],
            'phone'             => $validated['phone'],
            'dob'               => $validated['dob'],
            'gender'            => $validated['gender'],
            'address'           => $validated['address'],
            'jersey_size'       => $validated['jersey_size'],
            'emergency_contact' => $validated['emergency_contact'],
            'category'          => $validated['category'],
        ]);

        // 4. Determine price based on category
        $prices = [
            '3K' => 100000,
            '5K' => 150000,
            '10K' => 250000
        ];
        $price = $prices[$validated['category']];

        // 5. Generate Ticket
        $categoryCount = \App\Models\Participant::where('category', $validated['category'])
                                                ->whereHas('ticket')
                                                ->count();
        $newNumber = $categoryCount + 1;
        $ticketCode = 'FR2026-' . $validated['category'] . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        $ticket = \App\Models\Ticket::create([
            'participant_id' => $participant->id,
            'ticket_code'    => $ticketCode,
            'status'         => 'pending'
        ]);

        // 6. Save Proof of Payment file
        $path = $request->file('proof')->store('proofs', 'public');

        // 7. Create Payment record
        $payment = \App\Models\Payment::create([
            'ticket_id'        => $ticket->id,
            'amount'           => $price,
            'payment_method'   => $validated['payment_method'],
            'payment_status'   => 'waiting_verification',
            'proof_of_payment' => $path
        ]);

        return redirect()->route('ticket.show', ['ticket_code' => $ticket->ticket_code])
            ->with('success', 'Pendaftaran berhasil! Bukti pembayaran telah diunggah. Silakan tunggu verifikasi admin.');
    }

    public function checkout($participant_id)
    {
        $participant = \App\Models\Participant::findOrFail($participant_id);
        
        $prices = [
            '3K' => 100000,
            '5K' => 150000,
            '10K' => 250000
        ];
        
        $price = $prices[$participant->category];

        $ticket = \App\Models\Ticket::where('participant_id', $participant->id)->first();
        
        if (!$ticket) {
            $categoryCount = \App\Models\Participant::where('category', $participant->category)
                                                    ->whereHas('ticket')
                                                    ->count();
            $newNumber = $categoryCount + 1;
            $ticketCode = 'FR2026-' . $participant->category . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            $ticket = \App\Models\Ticket::create([
                'participant_id' => $participant->id,
                'ticket_code' => $ticketCode,
                'status' => 'pending'
            ]);
        }

        // Create Payment record
        $payment = \App\Models\Payment::firstOrCreate(
            ['ticket_id' => $ticket->id],
            ['amount' => $price, 'payment_status' => 'pending']
        );

        return view('checkout', compact('participant', 'ticket', 'payment', 'price'));
    }
}
