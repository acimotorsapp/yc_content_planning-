<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <title>YC Content Planning</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-gray-900 min-h-screen flex flex-col selection:bg-blue-500/20">
        
        <!-- Background Ambient Glows -->
        <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
            <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-blue-400/10 rounded-full blur-[150px]"></div>
            <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-indigo-400/10 rounded-full blur-[150px]"></div>
        </div>

        <!-- Navigation -->
        <header class="w-full border-b border-gray-200/80 bg-white/80 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between gap-3">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center text-xl font-black shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                        Y
                    </div>
                    <span class="text-sm sm:text-xl font-bold tracking-wider sm:tracking-widest text-gray-900 uppercase group-hover:text-blue-600 transition-colors truncate">YC Content Planning</span>
                </a>

                <!-- Auth Links -->
                <nav class="flex items-center gap-2 sm:gap-4 shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-gray-700 hover:text-blue-600 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-blue-600 transition-colors">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-bold px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-xs">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex flex-col items-center justify-center px-4 sm:px-6 relative pt-8 sm:pt-12">
            
            <div class="max-w-3xl w-full text-center space-y-6 sm:space-y-8">
                <!-- Badge -->
                <div class="inline-flex items-center justify-center px-3 sm:px-4 py-1.5 rounded-full border border-blue-200 bg-blue-50 shadow-xs text-[11px] sm:text-sm font-bold text-blue-700 text-left">
                    <span class="w-2 h-2 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                    Centralized Workspace for Product & Digital Teams
                </div>

                <!-- Headline -->
                <h1 class="text-4xl sm:text-5xl md:text-7xl font-black tracking-tight text-gray-900 leading-[1.1]">
                    Master your <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Content Pipeline.</span>
                </h1>

                <!-- Subheadline -->
                <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto font-medium">
                    The ultimate scheduling and strategy dashboard for YC Content Planning's internal marketing teams. Plan, align, and execute without conflicts.
                </p>

                <!-- CTA -->
                <div class="pt-4 sm:pt-6 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-white bg-blue-600 rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center">
                            Go to Dashboard
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 text-base font-bold text-white bg-blue-600 rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center">
                            Sign In to Workspace
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Decorative Dashboard Preview -->
            <div class="mt-10 sm:mt-16 w-full max-w-5xl rounded-2xl sm:rounded-3xl border border-gray-200 bg-white shadow-xl overflow-hidden p-2 relative">
                <div class="w-full h-8 border-b border-gray-100 flex items-center px-4 gap-2 mb-2 bg-slate-50 rounded-t-2xl">
                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                </div>
                <!-- Abstract Table representation -->
                <div class="p-4 sm:p-6 space-y-3 sm:space-y-4 opacity-40">
                    <div class="h-6 w-1/4 bg-gray-200 rounded-md"></div>
                    <div class="h-10 w-full bg-gray-100 rounded-xl"></div>
                    <div class="h-10 w-full bg-gray-100 rounded-xl"></div>
                    <div class="h-10 w-full bg-gray-100 rounded-xl"></div>
                </div>
                
                <!-- Fade out gradient at bottom -->
                <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
            </div>

        </main>

        <footer class="w-full text-center px-4 py-6 sm:py-8 text-xs sm:text-sm text-gray-500 font-medium">
            &copy; {{ date('Y') }} YC Content Planning. All rights reserved.
        </footer>
    </body>
</html>
