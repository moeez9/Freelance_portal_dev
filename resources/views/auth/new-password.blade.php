@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-xl">
        <form method="POST" action="{{ route('password.store') }}" class="bg-white p-6 rounded-lg border border-line space-y-4">
            @csrf
            <h4 class="heading4">Set New Password</h4>
            <p class="text-sm text-secondary">OTP verified. Enter your new password below.</p>

            @if (session('status'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <input type="email" name="email" value="{{ old('email', $email) }}" required readonly class="w-full border-gray-300 rounded-md bg-gray-100" placeholder="Email">
            @error('email')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <input type="password" name="password" required class="w-full border-gray-300 rounded-md" placeholder="New Password">
            @error('password')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <input type="password" name="password_confirmation" required class="w-full border-gray-300 rounded-md" placeholder="Confirm Password">
            <button class="button-main">Reset Password</button>
        </form>
    </div>
</section>
@endsection

