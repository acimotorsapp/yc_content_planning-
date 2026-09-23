<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg sm:text-2xl text-gray-900 leading-tight">
            {{ __('Send Email') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Send Email</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Email will be sent using the application mail configuration.</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl sm:rounded-3xl shadow-sm overflow-hidden max-w-3xl">
            <div class="px-5 sm:px-8 py-5 sm:py-6 border-b border-gray-100 bg-slate-50/70 flex items-center space-x-3 sm:space-x-4">
                <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-center text-blue-600 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Manual Email</h3>
                    <p class="text-sm text-gray-500 font-medium mt-0.5">Compose and send a one-time email.</p>
                </div>
            </div>

            <div class="px-5 sm:px-8 py-5 sm:py-6">
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                        Please fix the highlighted fields and try again.
                    </div>
                @endif

                <form action="{{ route('admin.send-email.send') }}" method="POST" class="space-y-6" x-data="{ submitting: false, ccEmails: @js(old('cc', [''])), addCc() { this.ccEmails.push(''); }, removeCc(index) { this.ccEmails.splice(index, 1); if (this.ccEmails.length === 0) this.ccEmails.push(''); } }" @submit="submitting = true">
                    @csrf

                    <div>
                        <label for="to" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">To Email</label>
                        <input id="to" type="email" name="to" value="{{ old('to') }}" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium @error('to') border-rose-300 @enderror">
                        @error('to')
                            <p class="text-xs text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-3">CC</label>
                        <div class="space-y-3">
                            <template x-for="(email, index) in ccEmails" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="email" name="cc[]" x-model="ccEmails[index]" placeholder="Optional CC email address" class="flex-1 bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium">
                                    <button type="button" @click="removeCc(index)" class="p-2.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-colors shrink-0" title="Remove Email">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="addCc" class="inline-flex items-center justify-center px-5 py-2.5 font-bold text-white bg-slate-800 border border-transparent rounded-xl hover:bg-slate-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-1">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add CC
                            </button>
                        </div>
                        @error('cc.*')
                            <p class="text-xs text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-3 font-medium">Blank, duplicate, and matching To addresses are ignored before sending.</p>
                    </div>

                    <div>
                        <label for="subject" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Subject</label>
                        <input id="subject" type="text" name="subject" value="{{ old('subject') }}" maxlength="255" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium @error('subject') border-rose-300 @enderror">
                        @error('subject')
                            <p class="text-xs text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="body" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Email Body</label>
                        <textarea id="body" name="body" rows="12" required class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium resize-y @error('body') border-rose-300 @enderror">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="text-xs text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-5 sm:pt-6 border-t border-gray-100 flex items-center justify-end">
                        <button type="submit" :disabled="submitting" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed shadow-md shadow-blue-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span x-show="!submitting">Send Email</span>
                            <span x-show="submitting" x-cloak>Sending...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
