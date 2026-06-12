<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming OTP password reset request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.email' => 'Please enter a valid email address otherwise you can signup.',
            'email.exists' => 'Your email address is not registered. Please enter a registered email address',
        ]);

        // Force reset emails to be sent from configured admin identity.
        Config::set('mail.from.address', (string) config('payments.manual_admin.email', 'abdulmoizakhter9@gmail.com'));
        Config::set('mail.from.name', (string) config('payments.manual_admin.name', 'Abdul Moiz Akhter'));

        $email = strtolower((string) $request->email);

        $block = DB::table('email_security_blocks')
            ->where('email', $email)
            ->where('blocked_until', '>', now())
            ->first();

        if ($block) {
            throw ValidationException::withMessages([
                'email' => 'This email is blocked for 2 hours due to OTP failure. Please try again after 2 hours.',
            ]);
        }

        $user = User::where('email', $email)->first();
        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => password_hash($otp, PASSWORD_BCRYPT),
                'created_at' => CarbonImmutable::now(),
            ]
        );

        Mail::raw(
            "Your password reset OTP is: {$otp}\n\nThis OTP expires in 60 minutes.",
            fn ($message) => $message->to($email)->subject('Password Reset OTP')
        );

        $status = config('mail.default') === 'log'
            ? 'OTP generated. Please contact support to receive the OTP since email logging is enabled.'
            : 'OTP sent to your email address. Please check your inbox and spam folder.';

        return redirect()->route('password.reset')->with([
            'status' => $status,
            'email' => $email,
        ]);
    }
}
