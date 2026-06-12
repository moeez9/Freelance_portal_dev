@extends('layouts.app')

@section('content')
<section class="breadcrumb">
            <div class="breadcrumb_inner relative  lg:py-20 py-14">
                <div class="breadcrumb_bg absolute top-0 left-0 w-full h-full">
                    <img src="https://freelanhub.vercel.app/assets/images/components/breadcrumb_candidate.webp" alt="breadcrumb_candidate" class="w-full h-full object-cover" />
                </div>
                <div class="container relative h-full">
                    <div class="breadcrumb_content flex flex-col items-start justify-center xl:w-[1000px] lg:w-[848px] md:w-5/6 w-full h-full">
                        <div class="list_breadcrumb flex items-center gap-2 animate animate_top" style="--i: 1">
                            <a href="{{ url('/') }}" class="caption1 text-white">Home</a>
                            <span class="caption1 text-white opacity-40">/</span>
                            <span class="caption1 text-white">Pages</span>
                            <span class="caption1 text-white opacity-40">/</span>
                            <span class="caption1 text-white opacity-60">Login</span>
                        </div>
                        <h3 class="heading3 text-white mt-2 animate animate_top" style="--i: 2">Login</h3>
                    </div>
                </div>
            </div>
        </section>
@if(false)
<div x-data="{ show: true }"
     x-show="show"
     x-transition
     class="fixed inset-0 flex items-center justify-center z-50">
    <div class="relative w-[90%] max-w-md">
        <div class="bg-surface border border-primary rounded-xl px-6 py-5 shadow-lg">

            <!-- Close button with Alpine -->
            <button @click="show = false"
                   class="absolute top-3 right-4 cursor-pointer text-2xl text-surface1 hover:text-primary transition-colors">
                ×
            </button>

            <div class="flex gap-3">
                <span class="text-primary text-xl">⚠</span>
                <div>
                    <h4 class="text-button font-semibold">
                        Something went wrong
                    </h4>
                    <ul class="mt-2 text-surface1 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endif



@if(false)
<div x-data="{ show: true }"
     x-show="show"
     x-transition
     class="fixed inset-0 flex items-center justify-center z-50">
    <div class="relative w-[90%] max-w-md">
        <div class="bg-primary/10 border border-primary rounded-xl px-6 py-5">

            <!-- Close button with Alpine.js -->
            <button @click="show = false"
                   class="absolute top-3 right-4 cursor-pointer text-2xl text-primary
                          hover:text-primary/70 transition-colors">
                ×
            </button>

            <div class="flex gap-3">
                <span class="text-primary text-xl">ℹ</span>
                <p class="text-button font-medium">
                    {{ session('info') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endif

        <section class="form_login lg:py-20 sm:py-14 py-10">
            <div class="container flex items-center justify-center">
                <div class="content sm:w-[448px] w-full">
                    @php
                        $redirectValue = old('redirect_to', $redirectTo ?? request('redirect_to'));
                        $signupParams = [];
                        if (!empty($redirectValue)) {
                            $signupParams['redirect_to'] = $redirectValue;
                        }
                        $roleHint = $preferredRole ?? request('role');
                        if (in_array($roleHint, ['candidate', 'employer'], true)) {
                            $signupParams['role'] = $roleHint;
                        }
                    @endphp
                    <h3 class="heading3 text-center">Log In</h3>
                    <form method="POST" action="{{ route('login') }}" class="form mt-6">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ $redirectValue }}">
                        <div class="form-group">
                            <label for="username">Email address*</label>
                            <input id="username" type="email" name="email" value="{{ old('email') }}" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Email address*" required />
                            @error('email')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group mt-6">
                            <label for="password">Password*</label>
                            <input id="password" type="password" name="password" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Password*" required />
                        </div>
                        <div class="flex items-center justify-between mt-6">
                            <div class="sub-input-checkbox flex items-center gap-2">
                                <input id="checkbox" type="checkbox" name="remember" />
                                <label for="checkbox" class="text-surface1">Remember me</label>
                            </div>
                            <a class="text-primary hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
                        </div>
                        <div class="block-button mt-6">
                            <button class="button-main bg-primary w-full text-center" type="submit">Login</button>
                        </div>
                        <div class="navigate flex items-center justify-center gap-2 mt-6">
                            <span class="text-surface1">Not registered yet?</span>
                            <a class="text-button hover:underline" href="{{ route('register', $signupParams) }}">Sign Up</a>
                        </div>

                    </form>
                </div>
            </div>
        </section>


        <button class="scroll-to-top-btn"><span class="ph-bold ph-caret-up"></span></button>
@endsection
