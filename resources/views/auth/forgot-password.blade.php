@extends('layouts.app')

@section('suppressFlashModal', '1')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-xl">
        @if ($errors->has('email'))
            <div x-data="{ show: true }" x-show="show" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                <div class="relative w-full max-w-md rounded-xl border border-red-200 bg-white p-6 shadow-lg">
                    <button @click="show = false" class="absolute right-3 top-2 text-2xl text-gray-500 hover:text-gray-700" type="button">&times;</button>
                    <h5 class="text-lg font-semibold text-red-700">Email Error</h5>
                    <p class="mt-2 text-sm text-gray-700">{{ $errors->first('email') }}</p>
                    <p class="mt-3 text-sm text-gray-700">Need an account? <a href="{{ route('register') }}" class="font-semibold text-primary underline">Sign up</a></p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="bg-white p-6 rounded-lg border border-line space-y-4">
            @csrf
            <h4 class="heading4">Forgot Password</h4>
            <p class="text-sm text-secondary">
                Please enter your email address and you will receive an OTP to reset your password.
            </p>

            @if (session('status'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-gray-300 rounded-md" placeholder="Email">
            @error('email')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
            <button class="button-main">Send OTP</button>
        </form>
    </div>
</section>
@endsection
