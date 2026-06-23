<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function submitRegistration(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'required|string',
            'jersey_size' => 'required|in:S,M,L,XL,XXL',
            'emergency_contact' => 'required|string|max:255',
            'category' => 'required|in:3K,5K,10K'
        ]);

        // Mock user creation or find
        $user = \App\Models\User::firstOrCreate(
            ['email' => $validated['email']],
            ['name' => $validated['fullname'], 'password' => bcrypt('password'), 'role' => 'participant']
        );

        // Assume Event ID 1 exists
        $event = \App\Models\Event::firstOrCreate(
            ['id' => 1],
            ['title' => 'FunRun 2026', 'date' => '2026-09-15', 'location' => 'City Square', 'quota' => 5000]
        );

        $participant = \App\Models\Participant::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'fullname' => $validated['fullname'],
            'phone' => $validated['phone'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'jersey_size' => $validated['jersey_size'],
            'emergency_contact' => $validated['emergency_contact'],
            'category' => $validated['category'],
        ]);

        return redirect()->route('checkout', ['participant_id' => $participant->id]);
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
