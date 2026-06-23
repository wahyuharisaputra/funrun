@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="glass-panel rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
        
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-bold mb-4">Participant Registration</h2>
            <p class="text-gray-400">Fill in your details accurately. Your BIB and Race Pack will be prepared based on this information.</p>
        </div>

        <form action="{{ url('/register-event') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name (as on ID)</label>
                    <input type="text" name="fullname" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">WhatsApp Number</label>
                    <input type="text" name="phone" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Date of Birth</label>
                    <input type="date" name="dob" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Gender</label>
                    <select name="gender" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <!-- Jersey Size -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Jersey Size</label>
                    <select name="jersey_size" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                </div>

                <!-- Emergency Contact -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Emergency Contact (Name - Phone)</label>
                    <input type="text" name="emergency_contact" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Run Category</label>
                    <select name="category" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="3K">3K Fun Walk (Rp 100,000)</option>
                        <option value="5K">5K Night Run (Rp 150,000)</option>
                        <option value="10K">10K Challenger (Rp 250,000)</option>
                    </select>
                </div>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Full Address</label>
                <textarea name="address" rows="3" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold text-lg py-4 rounded-xl hover:shadow-[0_0_20px_rgba(56,189,248,0.4)] transition-all">
                    Proceed to Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
