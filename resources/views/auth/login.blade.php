<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Welcome Back</h2>
            <p class="text-sm text-gray-500 mt-2">Enter your credentials to access your dashboard.</p>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium" 
                placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-[11px] font-bold text-gray-600 uppercase tracking-widest mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                class="w-full bg-slate-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-xs placeholder-gray-400 font-medium"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded bg-white border-gray-300 text-blue-600 shadow-xs focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors font-medium">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors focus:outline-none focus:underline" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="pt-1">
            <a href="{{ route('password.change') }}"
               class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold text-gray-700 bg-slate-50 border border-gray-300 rounded-xl hover:bg-white hover:border-blue-400 hover:text-blue-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Change password
            </a>
            <p class="text-[11px] text-gray-400 text-center mt-1.5">Know your current password? Change it without email.</p>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center px-6 py-3.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-md shadow-blue-500/20">
                Sign In
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </form>
</x-guest-layout>
