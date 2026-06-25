@extends('admin.layouts.admin')

@section('header_title', 'Manajemen Admin')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl">
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Header row --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-slate-500 text-sm">Total: <span class="font-bold text-slate-700">{{ count($admins) }}</span> akun admin</p>
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14M5 12h14"/></svg>
        Tambah Admin
    </button>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">No</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Nama</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Email</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Event yang Dikelola</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Tanggal Dibuat</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($admins as $i => $adm)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-4 text-slate-400">{{ $i + 1 }}</td>
                    <td class="px-5 py-4 font-semibold text-slate-800">{{ $adm->name }}</td>
                    <td class="px-5 py-4 text-slate-600">{{ $adm->email }}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $adm->event ? $adm->event->title : 'Belum Ditentukan' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-slate-500">{{ $adm->created_at->format('d M Y H:i') }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            {{-- Edit button --}}
                            <button onclick="openEdit({{ $adm->id }}, @js($adm->name), @js($adm->email), {{ $adm->event_id }})"
                                    class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                            {{-- Delete --}}
                            <form action="{{ route('admin.admins.destroy', $adm->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus akun admin \'{{ addslashes($adm->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Belum ada akun admin yang dibuat. Klik "Tambah Admin" untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== MODAL: ADD ADMIN ===== --}}
<div id="modalAdd" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-7 py-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Tambah Akun Admin Baru</h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.admins.store') }}" method="POST" class="px-7 py-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Contoh: Admin Semarang Run"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Kredensial</label>
                <input type="email" name="email" required placeholder="Contoh: semarang@setiket.com"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Event yang Dikelola</label>
                <select name="event_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="" disabled selected>Pilih Event...</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}">{{ $ev->title }} ({{ $ev->location }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium transition-colors">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL: EDIT ADMIN ===== --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-7 py-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Edit Akun Admin</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST" class="px-7 py-6 space-y-4">
            @csrf @method('PUT')
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Kredensial</label>
                <input type="email" name="email" id="edit_email" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru (Opsional)</label>
                <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Event yang Dikelola</label>
                <select name="event_id" id="edit_event_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}">{{ $ev->title }} ({{ $ev->location }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-medium transition-colors">Batal</button>
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-medium transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEdit(id, name, email, eventId) {
        const modal = document.getElementById('modalEdit');
        const form = document.getElementById('editForm');
        
        form.action = `/admin/admins/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_event_id').value = eventId;
        
        modal.classList.remove('hidden');
    }
</script>

@endsection
