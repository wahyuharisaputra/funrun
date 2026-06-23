@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#0f172a]"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-20">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-panel border-blue-500/30 text-blue-400 mb-8 animate-bounce">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
            </span>
            Registrations Open Now!
        </div>
        
        <h1 class="text-6xl md:text-8xl font-extrabold tracking-tighter mb-6">
            The Ultimate <br/>
            <span class="gradient-text">City Night Run</span>
        </h1>
        
        <p class="text-xl md:text-2xl text-gray-300 mb-10 max-w-3xl mx-auto font-light leading-relaxed">
            Join 5000+ runners on September 15, 2026. Experience the city lights like never before with exclusive glow-in-the-dark race packs.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('register') }}" class="w-full sm:w-auto bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:shadow-[0_0_30px_rgba(56,189,248,0.5)] transition-all transform hover:-translate-y-1">
                Register Now
            </a>
            <a href="#about" class="w-full sm:w-auto glass-panel text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-all">
                Learn More
            </a>
        </div>
        
        <!-- Countdown -->
        <div class="mt-20 grid grid-cols-4 gap-4 max-w-2xl mx-auto">
            <div class="glass-panel p-4 rounded-2xl text-center">
                <div class="text-4xl font-bold text-white mb-1">85</div>
                <div class="text-xs text-gray-400 uppercase tracking-widest">Days</div>
            </div>
            <div class="glass-panel p-4 rounded-2xl text-center">
                <div class="text-4xl font-bold text-white mb-1">12</div>
                <div class="text-xs text-gray-400 uppercase tracking-widest">Hours</div>
            </div>
            <div class="glass-panel p-4 rounded-2xl text-center">
                <div class="text-4xl font-bold text-white mb-1">45</div>
                <div class="text-xs text-gray-400 uppercase tracking-widest">Mins</div>
            </div>
            <div class="glass-panel p-4 rounded-2xl text-center">
                <div class="text-4xl font-bold text-white mb-1">30</div>
                <div class="text-xs text-gray-400 uppercase tracking-widest">Secs</div>
            </div>
        </div>
    </div>
</section>

<!-- Tickets Section -->
<section id="tickets" class="py-24 relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Choose Your <span class="gradient-text">Journey</span></h2>
            <p class="text-xl text-gray-400">Select the perfect category for your fitness level.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- 3K -->
            <div class="glass-panel rounded-3xl p-8 relative overflow-hidden group hover:border-blue-500/50 transition-colors">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM9.8 8.9L7 23h2.1l1.8-8 2.1 2v6h2v-7.5l-2.1-2 .6-3C14.8 12 16.8 13 19 13v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1L6 8.3V13h2V9.6l1.8-.7"/></svg>
                </div>
                <h3 class="text-2xl font-bold mb-2">3K Fun Walk</h3>
                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-4xl font-bold text-white">Rp 100k</span>
                </div>
                <ul class="space-y-4 mb-8 text-gray-300">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Exclusive BIB Number
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Finisher Medal
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span class="text-gray-500 line-through">Race Jersey</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 px-6 rounded-xl border border-white/20 text-center font-semibold hover:bg-white/10 transition-colors">Select 3K</a>
            </div>

            <!-- 5K -->
            <div class="glass-panel rounded-3xl p-8 relative overflow-hidden transform md:-translate-y-4 border-purple-500/30 shadow-[0_0_30px_rgba(147,51,234,0.15)] group hover:border-purple-500/60 transition-colors">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-purple-500" fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM9.8 8.9L7 23h2.1l1.8-8 2.1 2v6h2v-7.5l-2.1-2 .6-3C14.8 12 16.8 13 19 13v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1L6 8.3V13h2V9.6l1.8-.7"/></svg>
                </div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs font-bold px-4 py-1 rounded-b-lg uppercase tracking-wider">
                    Most Popular
                </div>
                <h3 class="text-2xl font-bold mb-2 mt-4">5K Night Run</h3>
                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-4xl font-bold text-white">Rp 150k</span>
                </div>
                <ul class="space-y-4 mb-8 text-gray-300">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Exclusive BIB Number
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Finisher Medal
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Glow-in-dark Jersey
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 px-6 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 text-white text-center font-bold hover:shadow-lg transition-shadow">Select 5K</a>
            </div>

            <!-- 10K -->
            <div class="glass-panel rounded-3xl p-8 relative overflow-hidden group hover:border-pink-500/50 transition-colors">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM9.8 8.9L7 23h2.1l1.8-8 2.1 2v6h2v-7.5l-2.1-2 .6-3C14.8 12 16.8 13 19 13v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1L6 8.3V13h2V9.6l1.8-.7"/></svg>
                </div>
                <h3 class="text-2xl font-bold mb-2">10K Challenger</h3>
                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-4xl font-bold text-white">Rp 250k</span>
                </div>
                <ul class="space-y-4 mb-8 text-gray-300">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Everything in 5K
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Premium Goodie Bag
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Timing Chip
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 px-6 rounded-xl border border-white/20 text-center font-semibold hover:bg-white/10 transition-colors">Select 10K</a>
            </div>
        </div>
    </div>
</section>
@endsection
