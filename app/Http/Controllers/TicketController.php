<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function showTicket($ticket_code)
    {
        $ticket = \App\Models\Ticket::with(['participant.event'])->where('ticket_code', $ticket_code)->firstOrFail();
        
        return view('ticket', compact('ticket'));
    }
}
