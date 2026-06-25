@extends('admin.layouts.admin')

@section('header_title', 'Payment Verification')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-lg">Pending Payments</h3>
            <p class="text-sm text-slate-500">Verify uploaded payment proofs and validate tickets.</p>
        </div>
        
        <!-- Filter Form -->
        <form action="{{ route('admin.payments') }}" method="GET" class="flex items-center gap-2">
            <label for="status" class="text-sm text-slate-500 font-medium">Filter:</label>
            <select name="status" id="status" onchange="this.form.submit()" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="all" {{ $currentStatus == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="waiting_verification" {{ $currentStatus == 'waiting_verification' ? 'selected' : '' }}>Pending (Waiting)</option>
                <option value="paid" {{ $currentStatus == 'paid' ? 'selected' : '' }}>Approved (Paid)</option>
            </select>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4">Participant</th>
                    <th class="px-6 py-4">Ticket Code</th>
                    <th class="px-6 py-4">Amount & Method</th>
                    <th class="px-6 py-4 text-center">Proof of Payment</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($payments as $payment)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $payment->ticket->participant->fullname ?? '-' }}</div>
                        <div class="text-xs text-slate-500">{{ $payment->ticket->participant->phone ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs font-semibold">
                        {{ $payment->ticket->ticket_code ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        <div class="text-xs text-slate-500 uppercase">{{ $payment->payment_method ?? 'Unknown' }}</div>
                        @if($payment->payment_status === 'paid')
                            <span class="inline-block mt-1 bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded">Verified</span>
                        @elseif($payment->payment_status === 'waiting_verification')
                            <span class="inline-block mt-1 bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded">Pending Verification</span>
                        @else
                            <span class="inline-block mt-1 bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded">{{ $payment->payment_status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($payment->proof_of_payment)
                            <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-xs font-medium">View Image</a>
                        @else
                            <span class="text-slate-400 text-xs">No proof uploaded</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($payment->payment_status === 'waiting_verification')
                            <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to approve this payment and validate the ticket?');">
                                @csrf
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium transition-colors">
                                    Verify & Approve
                                </button>
                            </form>
                        @elseif($payment->payment_status === 'paid')
                            <div class="flex flex-col gap-1.5 justify-center items-center">
                                <button disabled class="bg-slate-100 text-slate-400 px-4 py-1.5 rounded-lg text-xs font-medium cursor-not-allowed w-full">
                                    Approved
                                </button>
                                <a href="{{ route('ticket.show', $payment->ticket->ticket_code) }}" target="_blank" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-1.5 rounded-lg text-xs font-medium transition-colors w-full flex items-center justify-center gap-1">
                                    View E-Ticket
                                </a>
                                @php
                                    $waNumber = preg_replace('/^0/', '62', $payment->ticket->participant->phone);
                                    $waText = urlencode("🎉 *PEMBAYARAN BERHASIL* 🎉\n\nHalo *{$payment->ticket->participant->fullname}*,\n\nPembayaran tiket FunRun 2026 Anda telah diverifikasi!\n\nLink E-Ticket & QR Code:\n" . route('ticket.show', $payment->ticket->ticket_code) . "\n\nTerima kasih!");
                                @endphp
                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-medium transition-colors w-full flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                    WA Manual
                                </a>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
                
                @if($payments->isEmpty())
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">No payment records found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
