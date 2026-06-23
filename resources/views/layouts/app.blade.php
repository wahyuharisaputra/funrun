<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FunRun 2026')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-text {
            background: linear-gradient(to right, #38bdf8, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col relative overflow-x-hidden">
    <!-- Background Elements -->
    <div class="fixed inset-0 z-[-1] pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-600/20 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-purple-600/20 blur-[120px]"></div>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-panel border-b-0 border-white/5 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer" onclick="window.location.href='{{ route('home') }}'">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center font-bold text-xl shadow-lg shadow-blue-500/30">
                        FR
                    </div>
                    <span class="font-bold text-2xl tracking-tight">FunRun <span class="gradient-text">2026</span></span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}#about" class="text-gray-300 hover:text-white transition-colors font-medium">About</a>
                    <a href="{{ route('home') }}#route" class="text-gray-300 hover:text-white transition-colors font-medium">Route</a>
                    <a href="{{ route('home') }}#tickets" class="text-gray-300 hover:text-white transition-colors font-medium">Tickets</a>
                    <a href="{{ route('home') }}#faq" class="text-gray-300 hover:text-white transition-colors font-medium">FAQ</a>
                    <a href="{{ route('register') }}" class="bg-white text-slate-900 hover:bg-gray-100 px-6 py-2.5 rounded-full font-semibold transition-all transform hover:scale-105 shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                        Register Now
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="glass-panel border-t border-white/10 mt-20 py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center gap-2 mb-4 md:mb-0">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center font-bold text-sm shadow-lg">
                        FR
                    </div>
                    <span class="font-bold text-xl">FunRun 2026</span>
                </div>
                <div class="text-gray-400 text-sm">
                    &copy; 2026 FunRun Event Organizer. All rights reserved.
                </div>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Instagram</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Twitter</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-lg', 'bg-slate-900/80');
            } else {
                nav.classList.remove('shadow-lg', 'bg-slate-900/80');
            }
        });
    </script>
</body>
</html>
