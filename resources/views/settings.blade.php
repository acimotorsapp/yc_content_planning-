<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg sm:text-2xl text-gray-900 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12">


        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Email Configuration</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Configure global application settings and integrations.</p>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="bg-white border border-gray-200 rounded-2xl sm:rounded-3xl shadow-sm overflow-hidden max-w-3xl">
            <div class="px-5 sm:px-8 py-5 sm:py-6 border-b border-gray-100 bg-slate-50/70 flex items-center space-x-3 sm:space-x-4">
                <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Mail Configuration</h3>
                    <p class="text-sm text-gray-500 font-medium mt-0.5">Setup SMTP credentials for system notifications.</p>
                </div>
            </div>
            
            <div class="px-5 sm:px-8 py-5 sm:py-6">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Mailer</label>
                            <input type="text" name="MAIL_MAILER" value="{{ $settings['MAIL_MAILER'] ?? env('MAIL_MAILER', 'smtp') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Host</label>
                            <input type="text" name="MAIL_HOST" value="{{ $settings['MAIL_HOST'] ?? env('MAIL_HOST', 'sandbox.smtp.mailtrap.io') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Port</label>
                            <input type="number" name="MAIL_PORT" value="{{ $settings['MAIL_PORT'] ?? env('MAIL_PORT', '2525') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Username</label>
                            <input type="text" name="MAIL_USERNAME" value="{{ $settings['MAIL_USERNAME'] ?? env('MAIL_USERNAME') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail Password</label>
                            <input type="password" name="MAIL_PASSWORD" value="{{ $settings['MAIL_PASSWORD'] ?? env('MAIL_PASSWORD') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Mail From Address</label>
                            <input type="email" name="MAIL_FROM_ADDRESS" value="{{ $settings['MAIL_FROM_ADDRESS'] ?? env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME')) }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                        </div>
                    </div>

                    @php
                        $defaultCcList = "mirajul@aci-bd.com,richard@aci-bd.com,adhikary@aci-bd.com,efaz@aci-bd.com,Sourav.Bikash@aci-bd.com,Sultana.Nishi@aci-bd.com,Swagata@aci-bd.com,arnob@aci-bd.com,Nabil.Sarker@aci-bd.com,Abu.siddik@aci-bd.com,priasa@aci-bd.com,azmyen@aci-bd.com,Ashif.Ahmed@aci-bd.com";
                        $ccString = $settings['MAIL_CC_ADDRESS'] ?? $defaultCcList;
                    @endphp

                    <div class="grid grid-cols-1 gap-6" x-data="{ 
                        emails: '{{ $ccString }}'.split(',').map(e => e.trim()).filter(e => e),
                        newEmail: '',
                        addEmail() {
                            if (this.newEmail && !this.emails.includes(this.newEmail.trim())) {
                                this.emails.push(this.newEmail.trim());
                                this.newEmail = '';
                            }
                        },
                        removeEmail(index) {
                            this.emails.splice(index, 1);
                        }
                    }">
                        <div>
                            <input type="hidden" name="MAIL_CC_ADDRESS" :value="emails.join(',')">
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-3">Mail CC Address</label>
                            
                            <div class="space-y-3">
                                <template x-for="(email, index) in emails" :key="index">
                                    <div class="flex items-center gap-2">
                                        <input type="email" x-model="emails[index]" required class="flex-1 bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                        <button type="button" @click="removeEmail(index)" class="p-2.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors shrink-0" title="Remove Email">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                                
                                <div class="flex items-center gap-2 pt-1">
                                    <input type="email" x-model="newEmail" @keydown.enter.prevent="addEmail" placeholder="Add new email address..." class="flex-1 bg-white border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    <button type="button" @click="addEmail" class="shrink-0 inline-flex items-center justify-center px-5 py-2.5 font-bold text-white bg-slate-800 border border-transparent rounded-xl hover:bg-slate-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-1">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Add CC
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-3 font-medium">Edit an email directly, or add a new one. Click the trash icon to remove.</p>
                        </div>
                    </div>
                    
                    <div class="pt-5 sm:pt-6 border-t border-gray-100 flex items-center justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Save Mail Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
