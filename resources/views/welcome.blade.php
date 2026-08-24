<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>YC Content Planning</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-[#0a0a0a] text-gray-800 dark:text-gray-300 min-h-screen flex flex-col selection:bg-blue-500/30">
        
        <!-- Background Ambient Glows -->
        <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
            <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[150px]"></div>
            <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-teal-600/10 rounded-full blur-[150px]"></div>
        </div>

        <!-- Navigation -->
        <header class="w-full border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-[#0a0a0a]/50 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-xl font-black shadow-[0_0_15px_rgba(255,255,255,0.2)] text-black group-hover:scale-105 transition-transform">
                        Y
                    </div>
                    <span class="text-xl font-bold tracking-widest text-white uppercase group-hover:text-gray-800 dark:text-gray-300 transition-colors">YC Content Planning</span>
                </a>

                <!-- Auth Links -->
                <nav class="flex items-center gap-6">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-white transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-white transition-colors">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-semibold px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors border border-gray-200 dark:border-white/5">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex flex-col items-center justify-center px-6 relative">
            
            <div class="max-w-3xl w-full text-center space-y-8">
                <!-- Badge -->
                <div class="inline-flex items-center justify-center px-4 py-1.5 rounded-full border border-gray-300 dark:border-white/10 bg-white/5 backdrop-blur-sm shadow-[0_0_20px_rgba(255,255,255,0.05)] text-sm font-medium text-gray-800 dark:text-gray-300">
                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-2 shadow-[0_0_10px_rgba(59,130,246,0.8)]"></span>
                    Centralized Workspace for Product & Digital Teams
                </div>

                <!-- Headline -->
                <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white leading-[1.1]">
                    Master your <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-400">Content Pipeline.</span>
                </h1>

                <!-- Subheadline -->
                <p class="text-lg md:text-xl text-gray-500 max-w-2xl mx-auto font-medium">
                    The ultimate scheduling and strategy dashboard for YC Content Planning's internal marketing teams. Plan, align, and execute without conflicts.
                </p>

                <!-- CTA -->
                <div class="pt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-black bg-white rounded-xl hover:bg-gray-200 transition-all shadow-[0_0_20px_rgba(255,255,255,0.15)] hover:shadow-[0_0_30px_rgba(255,255,255,0.25)] flex items-center justify-center">
                            Go to Dashboard
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-black bg-white rounded-xl hover:bg-gray-200 transition-all shadow-[0_0_20px_rgba(255,255,255,0.15)] hover:shadow-[0_0_30px_rgba(255,255,255,0.25)] flex items-center justify-center">
                            Sign In to Workspace
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Decorative Dashboard Preview -->
            <div class="mt-20 w-full max-w-5xl rounded-2xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#111]/80 backdrop-blur-xl shadow-2xl overflow-hidden p-2">
                <div class="w-full h-8 border-b border-gray-200 dark:border-white/5 flex items-center px-4 gap-2 mb-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/50"></div>
                </div>
                <!-- Abstract Table representation -->
                <div class="p-6 space-y-4 opacity-50">
                    <div class="h-6 w-1/4 bg-white/10 rounded"></div>
                    <div class="h-10 w-full bg-white/5 rounded"></div>
                    <div class="h-10 w-full bg-white/5 rounded"></div>
                    <div class="h-10 w-full bg-white/5 rounded"></div>
                </div>
                
                <!-- Fade out gradient at bottom -->
                <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-[#0a0a0a] to-transparent"></div>
            </div>

        </main>

        <footer class="w-full text-center py-8 text-sm text-gray-600">
            &copy; {{ date('Y') }} YC Content Planning. All rights reserved.
        </footer>
    </body>
</html>
