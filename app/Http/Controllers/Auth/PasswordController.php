<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $email = strtolower((string) $request->user()->email);
        $isBlocked = DB::table('email_security_blocks')
            ->where('email', $email)
            ->where('blocked_until', '>', now())
            ->exists();

        if ($isBlocked) {
            return back()->withErrors([
                'current_password' => 'This account is temporarily blocked from password changes for 2 hours due to OTP failure.',
            ], 'updatePassword');
        }

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
