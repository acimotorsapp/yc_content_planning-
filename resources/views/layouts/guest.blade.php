<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    </head>
    <body class="font-sans antialiased bg-slate-50 text-gray-900">
        <!-- Background accents for a premium clean feel -->
        <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-400/10 rounded-full blur-[120px]"></div>
        </div>
        
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 px-4">
            <div>
                <a href="/" class="flex flex-col items-center group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-3xl font-black shadow-lg shadow-blue-500/20 text-white group-hover:scale-105 transition-transform">
                        Y
                    </div>
                    <span class="text-xl font-bold tracking-widest mt-4 text-gray-900 uppercase group-hover:text-blue-600 transition-colors">YC Content Planning</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white border border-gray-200/80 shadow-xl shadow-gray-200/50 rounded-2xl relative">
                {{ $slot }}
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if(session('status'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Notice',
                        text: @json(session('status')),
                        timer: 3500,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl shadow-xl' }
                    });
                @endif
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: @json(session('success')),
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl shadow-xl' }
                    });
                @endif
                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: @json(session('error')),
                        confirmButtonColor: '#2563eb',
                        customClass: { popup: 'rounded-2xl shadow-xl' }
                    });
                @endif
            });
        </script>
    </body>
</html>
