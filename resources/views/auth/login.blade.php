<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Welcome Back</h2>
            <p class="text-sm text-gray-500 mt-2">Enter your credentials to access your dashboard.</p>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-xl px-4 py-3 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600" 
                placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 dark:text-red-400 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                class="w-full bg-white dark:bg-[#1a1a1a] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white rounded-xl px-4 py-3 focus:bg-gray-50 dark:focus:bg-[#222] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm placeholder-gray-400 dark:placeholder-gray-600"
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 dark:text-red-400 text-sm" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded bg-white dark:bg-[#1a1a1a] border-gray-300 dark:border-white/10 text-blue-500 shadow-sm focus:ring-blue-500 focus:ring-offset-white dark:focus:ring-offset-[#111]" name="remember">
                <span class="ms-2 text-sm text-gray-500 group-hover:text-gray-300 transition-colors">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-400 hover:text-blue-300 transition-colors focus:outline-none focus:underline" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center px-6 py-3.5 text-sm font-bold text-white bg-gray-900 dark:text-black dark:bg-white rounded-xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-all focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#111] shadow-lg dark:shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-xl dark:hover:shadow-[0_0_25px_rgba(255,255,255,0.2)]">
                Sign In
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </form>
</x-guest-layout>
