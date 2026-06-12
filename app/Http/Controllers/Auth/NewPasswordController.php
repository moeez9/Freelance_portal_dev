<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display OTP verification view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Verify OTP and allow password reset step.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $email = strtolower((string) $request->email);

        $block = DB::table('email_security_blocks')
            ->where('email', $email)
            ->where('blocked_until', '>', now())
            ->first();

        if ($block) {
            return back()->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'This email is blocked for 2 hours due to invalid OTP attempts.',
                ]);
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $resetRecord) {
            return back()->withInput($request->only('email'))
                ->withErrors(['otp' => 'OTP invalid hai. Please try again and regenerate the request.']);
        }

        $isExpired = CarbonImmutable::parse($resetRecord->created_at)
            ->addMinutes(60)
            ->isPast();

        if ($isExpired || ! Hash::check($request->otp, $resetRecord->token)) {
            DB::table('email_security_blocks')->updateOrInsert(
                ['email' => $email],
                [
                    'blocked_until' => CarbonImmutable::now()->addHours(2),
                    'reason' => 'invalid_otp',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('password.request')
                ->withInput(['email' => $email])
                ->withErrors([
                    'email' => 'Invalid OTP. This email is now blocked for 2 hours. Try again later.',
                ]);
        }

        session([
            'password_reset_verified_email' => $email,
        ]);

        return redirect()->route('password.new')
            ->with('status', 'OTP verified successfully. Please set your new password.');
    }

    /**
     * Display new password form after OTP verification.
     */
    public function newPasswordForm(Request $request): RedirectResponse|View
    {
        $email = (string) session('password_reset_verified_email', '');

        if ($email === '') {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please verify OTP first.']);
        }

        return view('auth.new-password', ['email' => $email, 'request' => $request]);
    }

    /**
     * Handle final password update.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = strtolower((string) $request->email);
        $verifiedEmail = (string) session('password_reset_verified_email', '');

        if ($verifiedEmail === '' || $verifiedEmail !== $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please verify OTP again.']);
        }

        $block = DB::table('email_security_blocks')
            ->where('email', $email)
            ->where('blocked_until', '>', now())
            ->first();

        if ($block) {
            session()->forget('password_reset_verified_email');
            return redirect()->route('password.request')
                ->withErrors(['email' => 'This email is temporarily blocked for 2 hours.']);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            session()->forget('password_reset_verified_email');
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User account nahi mila.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget('password_reset_verified_email');

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Password reset successful. Please login.');
    }
}
