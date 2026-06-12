<?php

namespace App\Http\Controllers;

use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')
                ->with('error', 'Please logout from user account before admin login.');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')
                ->with('error', 'Admin login is blocked while user session is active.');
        }

        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = $this->findConfiguredAdmin($data['email']);
        if (!$admin || !$this->passwordMatches($data['password'], (string) $admin['password'])) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid admin credentials.']);
        }

        $request->session()->regenerate();
        $request->session()->put('admin_auth', [
            'email' => strtolower((string) $admin['email']),
        ]);

        $profile = AdminProfile::where('email', strtolower((string) $admin['email']))->first();
        if (!$profile || !$profile->name || !$profile->profile_pic) {
            return redirect()->route('admin.profile.setup');
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Admin logged out.');
    }

    private function findConfiguredAdmin(string $email): ?array
    {
        $needle = strtolower($email);
        foreach ((array) config('admin_auth.admins', []) as $admin) {
            if (strtolower((string) ($admin['email'] ?? '')) === $needle) {
                return $admin;
            }
        }

        return null;
    }

    private function passwordMatches(string $input, string $stored): bool
    {
        if ($stored === '') {
            return false;
        }

        if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon2')) {
            return Hash::check($input, $stored);
        }

        return hash_equals($stored, $input);
    }
}
