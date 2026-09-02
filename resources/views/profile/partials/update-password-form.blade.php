<section class="space-y-5 sm:space-y-6">
    <header class="flex items-start gap-3">
        <div class="w-10 h-10 shrink-0 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <div class="min-w-0">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">
                {{ __('Change Password') }}
            </h2>
            <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                {{ __('Enter your current password, then pick a new one. No email verification needed.') }}
            </p>
        </div>
    </header>

    @if ($errors->updatePassword->any())
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-2.5">
            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <ul class="text-sm font-semibold space-y-1 min-w-0">
                @foreach ($errors->updatePassword->all() as $error)
                    <li class="break-words">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-2.5">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm font-bold">{{ __('Password updated. Use the new one next time you sign in.') }}</span>
        </div>
    @endif

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">
                {{ __('Current Password') }}
            </label>
            <input id="update_password_current_password" name="current_password" type="password" required autocomplete="current-password"
                   class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium"
                   placeholder="{{ __('Your existing password') }}">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-600 text-sm" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
            <div>
                <label for="update_password_password" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">
                    {{ __('New Password') }}
                </label>
                <input id="update_password_password" name="password" type="password" required autocomplete="new-password"
                       class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium"
                       placeholder="{{ __('At least 8 characters') }}">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-600 text-sm" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">
                    {{ __('Confirm New Password') }}
                </label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                       class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs font-medium"
                       placeholder="{{ __('Repeat the new password') }}">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-600 text-sm" />
            </div>
        </div>

        <p class="text-xs text-gray-500 font-medium">
            {{ __('New password must be at least 8 characters.') }}
        </p>

        <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 sm:gap-3">
            <button type="submit" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 text-sm font-bold text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('Update Password') }}
            </button>
        </div>
    </form>
</section>
