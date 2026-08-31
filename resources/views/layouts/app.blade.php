<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ showCreateModal: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }} }">
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
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex">
            <!-- Sidebar Navigation -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">
                <!-- Page Heading (Optional Top Bar) -->
                <header class="bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200/80 sticky top-0 z-30">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        @isset($header)
                            <div class="text-xl font-bold text-gray-900 tracking-tight">{{ $header }}</div>
                        @else
                            <div class="text-xl font-bold text-gray-900 tracking-tight">Dashboard</div>
                        @endisset

                        <!-- Profile Display -->
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold text-xs">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                    <div class="max-w-7xl mx-auto">
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
            });
        </script>
    </body>
</html>
