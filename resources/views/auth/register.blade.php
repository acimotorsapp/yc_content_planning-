<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Create an Account</h2>
            <p class="text-sm text-gray-500 mt-1">Join the content planning workspace.</p>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="font-bold text-xs uppercase tracking-wider text-gray-600 mb-1.5" />
            <x-text-input id="name" class="block w-full bg-slate-50 border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 focus:bg-white font-medium" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 text-sm" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="font-bold text-xs uppercase tracking-wider text-gray-600 mb-1.5" />
            <x-text-input id="email" class="block w-full bg-slate-50 border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 focus:bg-white font-medium" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="font-bold text-xs uppercase tracking-wider text-gray-600 mb-1.5" />
            <x-text-input id="password" class="block w-full bg-slate-50 border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 focus:bg-white font-medium"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="font-bold text-xs uppercase tracking-wider text-gray-600 mb-1.5" />
            <x-text-input id="password_confirmation" class="block w-full bg-slate-50 border-gray-300 rounded-xl px-4 py-2.5 text-gray-900 focus:bg-white font-medium"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 text-sm" />
        </div>

        <div class="flex items-center justify-between pt-4">
            <a class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</x-guest-layout>
