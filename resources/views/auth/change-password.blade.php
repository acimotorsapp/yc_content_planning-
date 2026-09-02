<x-guest-layout>
    <form method="POST" action="{{ route('password.change.store') }}" class="space-y-6">
        @csrf

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Change Password</h2>
            <p class="text-sm text-gray-500 mt-2">Enter your current password and pick a new one. No email needed.</p>
        </div>

        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-2.5">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <ul class="text-sm font-semibold space-y-1 min-w-0">
                    @foreach ($errors->all() as $error)
                        <li class="break-words">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium"
                placeholder="you@example.com">
        </div>

        <!-- Current Password -->
        <div>
            <label for="current_password" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Current Password</label>
            <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium"
                placeholder="••••••••">
        </div>

        <!-- New Password -->
        <div>
            <label for="password" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">New Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium"
                placeholder="At least 8 characters">
        </div>

        <!-- Confirm New Password -->
        <div>
            <label for="password_confirmation" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium"
                placeholder="Repeat the new password">
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center px-6 py-3.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-md shadow-blue-500/20">
                Update Password
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>

        <p class="text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                &larr; Back to sign in
            </a>
        </p>
    </form>
</x-guest-layout>
