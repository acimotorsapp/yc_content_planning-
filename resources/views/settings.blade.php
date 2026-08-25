<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12">
        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 p-4 mb-6 rounded-2xl shadow-xs flex items-center" role="alert">
                <svg class="w-5 h-5 text-emerald-600 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-emerald-800 text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 p-5 mb-6 rounded-2xl shadow-xs" role="alert">
                <div class="flex">
                    <svg class="w-6 h-6 text-red-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <ul class="list-disc list-inside text-red-600 text-sm font-medium">
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
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">System Settings</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Configure global application settings and integrations.</p>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden max-w-3xl">
            <div class="px-8 py-6 border-b border-gray-100 bg-slate-50/70 flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Mail Configuration</h3>
                    <p class="text-sm text-gray-500 font-medium mt-0.5">Setup SMTP credentials for system notifications.</p>
                </div>
            </div>
            
            <div class="px-8 py-6">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Mailer</label>
                            <input type="text" name="MAIL_MAILER" value="{{ env('MAIL_MAILER', 'smtp') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Host</label>
                            <input type="text" name="MAIL_HOST" value="{{ env('MAIL_HOST', 'sandbox.smtp.mailtrap.io') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Port</label>
                            <input type="number" name="MAIL_PORT" value="{{ env('MAIL_PORT', '2525') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Username</label>
                            <input type="text" name="MAIL_USERNAME" value="{{ env('MAIL_USERNAME') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Password</label>
                        <input type="password" name="MAIL_PASSWORD" value="{{ env('MAIL_PASSWORD') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                    </div>
                    
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end">
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Save Mail Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
