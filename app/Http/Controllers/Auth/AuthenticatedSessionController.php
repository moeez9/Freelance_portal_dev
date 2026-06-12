<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        return view('login', [
            'redirectTo' => $request->query('redirect_to'),
            'preferredRole' => $request->query('role'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $redirectTo = (string) $request->input('redirect_to', '');
        if ($redirectTo !== '') {
            $path = parse_url($redirectTo, PHP_URL_PATH) ?? '';
            $query = parse_url($redirectTo, PHP_URL_QUERY);
            if (is_string($path) && str_starts_with($path, '/')) {
                $safeRedirect = $path . ($query ? ('?' . $query) : '');
                return redirect()->to($safeRedirect);
            }
        }

        return redirect()->route('dashboard');
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
