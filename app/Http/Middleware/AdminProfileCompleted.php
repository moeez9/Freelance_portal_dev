<?php

namespace App\Http\Middleware;

use App\Models\AdminProfile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProfileCompleted
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')
                ->with('error', 'Admin panel access is blocked while user session is active.');
        }

        $email = (string) ($request->session()->get('admin_auth.email') ?? '');
        if ($email === '') {
            return redirect()->route('admin.login');
        }

        $profile = AdminProfile::where('email', $email)->first();
        if (!$profile || !$profile->name || !$profile->profile_pic) {
            return redirect()->route('admin.profile.setup')
                ->with('error', 'Please complete admin profile setup first.');
        }

        return $next($request);
    }
}
