<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use carbon\Carbon;
use Illuminate\Support\Str;
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone_no' => ['nullable', 'string', 'max:30', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:candidate,employer'],
            'dob' => ['nullable', 'date', 'before_or_equal:' . Carbon::now()->subYears(18)->toDateString(),],['dob.before_or_equal' => 'You must be at least 18 years old to register.',],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'redirect_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $phone = $request->phone_no ?: ('03' . substr(str_replace('.', '', (string) microtime(true)), -9));
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => Str::limit($phone, 30, ''),
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'candidate',
            'dob' => $request->dob ?? Carbon::now()->subYears(20)->toDateString(),
            'gender' => $request->gender ?? 'other'
        ]);

        event(new Registered($user));

        Auth::login($user);

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
}
