<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ showCreateModal: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }}, sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#ffffff">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

        <style>
            [x-cloak] { display: none !important; }

            html, body { overflow-x: hidden; }
            body { -webkit-text-size-adjust: 100%; }

            /* App shell height: use the dynamic viewport height on mobile browsers */
            .app-shell { height: 100vh; height: 100dvh; }

            /* Lock the page behind the mobile drawer */
            body.drawer-open { overflow: hidden; }

            /* Stop iOS from zooming in when a field gets focus */
            @media (max-width: 767px) {
                input, select, textarea { font-size: 16px !important; }
                input[type="date"], input[type="datetime-local"], select { min-height: 44px; }
            }

            /* Full-bleed horizontal scrollers on phones */
            @media (max-width: 639px) {
                .scroll-bleed { margin-left: -0.75rem; margin-right: -0.75rem; padding-left: 0.75rem; padding-right: 0.75rem; }
            }

            /* Tables that stay tabular on phones: tighten cells so they fit */
            @media (max-width: 639px) {
                .compact-table th, .compact-table td {
                    padding-left: 0.75rem !important;
                    padding-right: 0.75rem !important;
                    padding-top: 0.65rem !important;
                    padding-bottom: 0.65rem !important;
                }
                .compact-table th { font-size: 0.6rem !important; }
            }

            /* Thin scrollbars for horizontal table scrollers */
            .nice-scroll { scrollbar-width: thin; -webkit-overflow-scrolling: touch; }
            .nice-scroll::-webkit-scrollbar { height: 6px; width: 6px; }
            .nice-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
            .nice-scroll::-webkit-scrollbar-track { background: transparent; }

            @media (max-width: 480px) {
                .swal2-popup { width: 92vw !important; font-size: 0.9rem !important; }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900" :class="sidebarOpen ? 'drawer-open' : ''">
        <div class="app-shell flex">
            <!-- Sidebar Navigation (off-canvas drawer on mobile) -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col app-shell overflow-hidden bg-gray-50 min-w-0">
                <!-- Page Heading (Top Bar) -->
                <header class="bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200/80 sticky top-0 z-30">
                    <div class="max-w-7xl mx-auto py-3 sm:py-4 px-3 sm:px-6 lg:px-8 flex justify-between items-center gap-2 sm:gap-4">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <!-- Mobile menu toggle -->
                            <button type="button"
                                    @click="sidebarOpen = true"
                                    aria-label="Open navigation menu"
                                    class="lg:hidden -ml-1 shrink-0 p-2 rounded-xl text-gray-600 border border-gray-200 hover:bg-gray-100 active:bg-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>

                            <div class="min-w-0 flex-1 overflow-hidden text-base sm:text-xl font-bold text-gray-900 tracking-tight">
                                @isset($header)
                                    {{ $header }}
                                @else
                                    Dashboard
                                @endisset
                            </div>
                        </div>

                        <!-- Profile Display + account menu -->
                        <div class="relative shrink-0" x-data="{ userMenu: false }" @click.outside="userMenu = false" @keydown.escape.window="userMenu = false">
                            <button type="button"
                                    @click="userMenu = !userMenu"
                                    :aria-expanded="userMenu"
                                    aria-haspopup="true"
                                    aria-label="Account menu"
                                    class="flex items-center gap-2 sm:gap-3 rounded-full sm:rounded-xl p-0.5 sm:px-2 sm:py-1.5 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                                <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                                <span class="hidden sm:block text-sm font-semibold text-gray-700 truncate max-w-[9rem] lg:max-w-none">{{ Auth::user()->name }}</span>
                                <svg class="hidden sm:block w-4 h-4 text-gray-400 transition-transform" :class="userMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div x-show="userMenu"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                                 class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl bg-white border border-gray-200 shadow-xl shadow-gray-300/40 ring-1 ring-black/5 overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-gray-100 bg-slate-50/70">
                                    <div class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                                    <div class="mt-1.5 inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ str_replace('_', ' ', Auth::user()->role) }}
                                    </div>
                                </div>

                                <a href="{{ route('profile.edit') }}"
                                   @click="userMenu = false"
                                   class="flex items-center gap-2.5 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profile
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 transition-colors text-left cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto overflow-x-hidden p-3 sm:p-6 lg:p-8">
                    <div class="max-w-7xl mx-auto w-full">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <!-- Global Create Event Modal for Sidebar Button -->
        @include('events.partials.create-modal')

        <!-- SweetAlert Notification Handlers -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Flash Success Message
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: @json(session('success')),
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-2xl shadow-xl'
                        }
                    });
                @endif

                // Flash Error Message
                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: @json(session('error')),
                        confirmButtonColor: '#2563eb',
                        customClass: {
                            popup: 'rounded-2xl shadow-xl',
                            confirmButton: 'px-5 py-2.5 rounded-xl font-bold'
                        }
                    });
                @endif

                // Flash Status Message
                @if(session('status'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Notice',
                        text: @json(session('status')),
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-2xl shadow-xl'
                        }
                    });
                @endif

                // Global SweetAlert Confirmation for Delete/Action Forms
                document.addEventListener('submit', function(e) {
                    var form = e.target;
                    if (form.classList.contains('delete-form') || form.hasAttribute('data-confirm')) {
                        e.preventDefault();
                        var message = form.getAttribute('data-confirm') || 'Are you sure you want to delete this? This action cannot be undone.';
                        var title = form.getAttribute('data-confirm-title') || 'Are you sure?';

                        Swal.fire({
                            title: title,
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true,
                            customClass: {
                                popup: 'rounded-2xl shadow-2xl',
                                confirmButton: 'px-5 py-2.5 rounded-xl font-bold bg-red-600 text-white hover:bg-red-700',
                                cancelButton: 'px-5 py-2.5 rounded-xl font-semibold bg-gray-200 text-gray-700 hover:bg-gray-300'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                });

                // Global AJAX Pagination
                document.addEventListener('click', function(e) {
                    let link = e.target.closest('nav[role="navigation"] a') || e.target.closest('.pagination a');
                    if (!link) return;

                    let container = link.closest('.bg-white.shadow-sm');
                    if (!container) return;
                    
                    e.preventDefault();
                    let url = link.href;
                    
                    // Add loading state
                    container.style.opacity = '0.6';
                    container.style.pointerEvents = 'none';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        
                        let currentContainers = Array.from(document.querySelectorAll('.bg-white.shadow-sm'));
                        let index = currentContainers.indexOf(container);
                        
                        let newContainers = Array.from(doc.querySelectorAll('.bg-white.shadow-sm'));
                        
                        if (index !== -1 && newContainers[index]) {
                            container.innerHTML = newContainers[index].innerHTML;
                            container.style.opacity = '1';
                            container.style.pointerEvents = 'auto';
                            window.history.pushState({}, '', url);
                        } else {
                            window.location.href = url;
                        }
                    })
                    .catch(() => {
                        window.location.href = url;
                    });
                });
            });
        </script>
    </body>
</html>
