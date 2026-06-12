<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')
                ->with('error', 'Admin panel access is blocked while user session is active.');
        }

        $admin = $request->session()->get('admin_auth');
        if (!is_array($admin) || empty($admin['email'])) {
            return redirect()->route('admin.login')
                ->with('error', 'Please login as admin.');
        }

        return $next($request);
    }
}
