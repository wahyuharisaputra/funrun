@extends('admin.layouts.admin')

@section('header_title', 'Participants Data')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h3 class="font-bold text-lg">All Registered Participants</h3>
        <div class="flex items-center gap-3">
            <!-- Filter Form -->
            <form action="{{ route('admin.participants') }}" method="GET" class="flex items-center gap-2">
                <label for="category" class="text-sm text-slate-500 font-medium hidden sm:block">Category:</label>
                <select name="category" id="category" onchange="this.form.submit()" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ $currentCategory == 'all' ? 'selected' : '' }}>All Categories</option>
                    <option value="3K" {{ $currentCategory == '3K' ? 'selected' : '' }}>3K Run</option>
                    <option value="5K" {{ $currentCategory == '5K' ? 'selected' : '' }}>5K Run</option>
                    <option value="10K" {{ $currentCategory == '10K' ? 'selected' : '' }}>10K Run</option>
                </select>
            </form>

            <a href="{{ route('admin.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export CSV
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Contact</th>
                    <th class="px-6 py-4 text-center">Category & Size</th>
                    <th class="px-6 py-4 text-center">Ticket Status</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($participants as $participant)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $participant->fullname }}</td>
                    <td class="px-6 py-4">
                        <div class="text-slate-800">{{ $participant->phone }}</div>
                        <div class="text-xs text-slate-500">{{ $participant->user->email ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="font-bold text-blue-600">{{ $participant->category }}</div>
                        <div class="text-xs text-slate-500">Jersey: {{ $participant->jersey_size }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($participant->ticket)
                            @if($participant->ticket->status === 'checked-in')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">Checked In</span>
                            @elseif($participant->ticket->status === 'valid')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Valid</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium">Pending</span>
                            @endif
                            <div class="text-xs text-slate-500 mt-1 font-mono">{{ $participant->ticket->ticket_code }}</div>
                        @else
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-medium">No Ticket</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.participants.edit', $participant->id) }}" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg text-xs font-medium transition-colors">Edit</a>
                            <form action="{{ route('admin.participants.delete', $participant->id) }}" method="POST" onsubmit="return confirm('Delete this participant? All their tickets and payments will be lost!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg text-xs font-medium transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($participants->isEmpty())
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">No participants found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
