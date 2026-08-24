<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12">
        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-white dark:bg-[#111] border border-green-500/30 p-4 mb-6 rounded-xl shadow-sm flex items-center" role="alert">
                <svg class="w-5 h-5 text-green-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 p-5 mb-6 rounded-xl shadow-sm" role="alert">
                <div class="flex">
                    <svg class="w-6 h-6 text-red-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <ul class="list-disc list-inside text-red-400 text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">System Settings</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Configure global application settings and integrations.</p>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="bg-white dark:bg-[#111] border border-gray-200 dark:border-white/5 rounded-xl shadow-sm overflow-hidden max-w-3xl">
            <div class="px-8 py-6 border-b border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-[#161616] flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-500/10 border border-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 shadow-[0_0_15px_rgba(59,130,246,0.15)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Mail Configuration</h3>
                    <p class="text-sm text-gray-500 font-medium mt-0.5">Setup SMTP credentials for system notifications.</p>
                </div>
            </div>
            
            <div class="px-8 py-6">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Mail Mailer</label>
                            <input type="text" name="MAIL_MAILER" value="{{ env('MAIL_MAILER', 'smtp') }}" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Mail Host</label>
                            <input type="text" name="MAIL_HOST" value="{{ env('MAIL_HOST', 'sandbox.smtp.mailtrap.io') }}" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Mail Port</label>
                            <input type="number" name="MAIL_PORT" value="{{ env('MAIL_PORT', '2525') }}" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Mail Username</label>
                            <input type="text" name="MAIL_USERNAME" value="{{ env('MAIL_USERNAME') }}" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-2">Mail Password</label>
                        <input type="password" name="MAIL_PASSWORD" value="{{ env('MAIL_PASSWORD') }}" required class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm">
                    </div>
                    
                    <div class="pt-6 border-t border-gray-200 dark:border-white/5 flex items-center justify-end">
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.3)] transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111]">
                            Save Mail Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
