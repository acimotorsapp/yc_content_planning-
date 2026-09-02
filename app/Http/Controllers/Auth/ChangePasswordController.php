<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Lets someone change their password from the login screen by proving they already
 * know the current one — no reset email, no verified address.
 *
 * Knowing the email and the current password is the same proof a normal sign-in
 * asks for, so this grants nothing extra. It is throttled like the login form,
 * otherwise it would be an unlimited oracle for guessing passwords.
 */
class ChangePasswordController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    public function create(): View
    {
        return view('auth.change-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->ensureIsNotRateLimited($request);

        $user = User::where('email', Str::lower($request->string('email')))->first();

        if (! $user || ! Hash::check($request->input('current_password'), $user->password)) {
            RateLimiter::hit($this->throttleKey($request));

            // Deliberately vague: never reveal whether the address exists.
            throw ValidationException::withMessages([
                'email' => __('Those details do not match our records.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('login')
            ->with('status', __('Password changed. Sign in with your new password.'));
    }

    private function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return 'change-password|' . Str::transliterate(Str::lower($request->string('email')) . '|' . $request->ip());
    }
}
