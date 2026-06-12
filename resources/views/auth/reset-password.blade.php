@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-xl">
        <form method="POST" action="{{ route('password.otp.verify') }}" class="bg-white p-6 rounded-lg border border-line space-y-4">
            @csrf
            <h4 class="heading4">Enter OTP</h4>
            <p class="text-sm text-secondary">Please enter the 6-digit OTP sent to your registered email.</p>

            @if (session('status'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <input type="email" name="email" value="{{ old('email', session('email', $request->email)) }}" required class="w-full border-gray-300 rounded-md" placeholder="Email">
            @error('email')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <input type="text" name="otp" value="{{ old('otp') }}" required maxlength="6" class="w-full border-gray-300 rounded-md" placeholder="6-digit OTP">
            @error('otp')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <button class="button-main">Verify OTP</button>
        </form>
    </div>
</section>
@endsection
