@extends('layouts.app')

@section('content')
<section class="breadcrumb">
            <div class="breadcrumb_inner relative lg:py-20 py-14">
                <div class="breadcrumb_bg absolute top-0 left-0 w-full h-full">
                    <img src="https://freelanhub.vercel.app/assets/images/components/breadcrumb_candidate.webp" alt="breadcrumb_candidate" class="w-full h-full object-cover" />
                </div>
                <div class="container relative h-full ">
                    <div class="breadcrumb_content flex flex-col items-start justify-center xl:w-[1000px] lg:w-[848px] md:w-5/6 w-full h-full">
                        <div class="list_breadcrumb flex items-center gap-2 animate animate_top" style="--i: 1">
                            <a href="{{ url('/') }}" class="caption1 text-white">Home</a>
                            <span class="caption1 text-white opacity-40">/</span>
                            <span class="caption1 text-white">Pages</span>
                            <span class="caption1 text-white opacity-40">/</span>
                            <span class="caption1 text-white opacity-60">Register</span>
                        </div>
                        <h3 class="heading3 text-white mt-2 animate animate_top" style="--i: 2">Register</h3>
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
        <section class="form_register lg:py-20 sm:py-14 py-10">
            <div class="container flex items-center justify-center">
                <div class="content sm:w-[448px] w-full">
                    @php
                        $preferredRole = request()->query('role');
                        $isEmployerPreferred = $preferredRole === 'employer';
                    @endphp
                    <h3 class="heading3 text-center">Create a free account</h3>
                    <div class="menu_tab w-full mt-8">
                        <ul class="list grid grid-cols-2 gap-5 w-full" role="tablist">
                            <li role="presentation">
                                <button class="tab_btn -fill -fill-primary w-full py-3 text-button text-center rounded bg-surface duration-300 hover:text-primary {{ $isEmployerPreferred ? '' : 'active' }}" id="tab_candidate" role="tab" aria-controls="candidate" aria-selected="{{ $isEmployerPreferred ? 'false' : 'true' }}">Candidate</button>
                            </li>
                            <li role="presentation">
                                <button class="tab_btn -fill -fill-primary w-full py-3 text-button text-center rounded bg-surface duration-300 hover:text-primary {{ $isEmployerPreferred ? 'active' : '' }}" id="tab_employer" role="tab" aria-controls="employer" aria-selected="{{ $isEmployerPreferred ? 'true' : 'false' }}">Employer</button>
                            </li>
                        </ul>
                    </div>
                    <div id="candidate" class="tab_list {{ $isEmployerPreferred ? '' : 'active' }}" role="tabpanel" aria-labelledby="tab_candidate" aria-hidden="{{ $isEmployerPreferred ? 'true' : 'false' }}">
                        <form class="form mt-6" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="form-group">
                                <input type="hidden" name="role" value="candidate">
                                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                                <label for="name">Full Name*</label>
                                <input id="name" type="text" name="name" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Enter Your Name" required />
                            </div>
                            <div class="form-group mt-6">
                                <label for="username">Candidate email address*</label>
                                <input id="username" type="email" name="email" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Email address*" required />
                            </div>
                            <div class="form-group mt-6">
                                <label for="phone_no">Phone Number*</label>
                                <input id="phone_no" type="text" name="phone_no" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Enter Phone Number*" required />
                            </div>
                            <div class="form-group mt-6">
                                <div class="flex flex-col gap-4">
                                    <div>
                                <label for="dob">Date of Birth*</label>
                                <input id="dob" required type="date" name="dob" class="form-control border-line px-4 h-[50px] rounded-lg">
                                    </div>
                                    <div>
                                <label for="gender">Gender*</label>
                                <select id="gender" required name="gender" class="form-control border-line px-8 h-[50px] rounded-lg">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            </div>
                            </div>
                            <div class="form-group mt-6">
                                <label for="password">Password*</label>
                                <input id="password" type="password" name="password" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Password*" required />
                            </div>
                            <div class="form-group mt-6">
                                <label for="confirmPassword">Confirm password*</label>
                                <input id="confirmPassword" type="password" name="password_confirmation" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Confirm password*" required />
                            </div>
                            <div class="flex items-center justify-between mt-6">
                                <div class="sub-input-checkbox flex items-center gap-2">
                                    <input id="checkbox" type="checkbox" name="checkbox" required/>
                                    <label for="checkbox" class="text-surface1">I agree to the <a href="{{ url('/terms') }}" class="text-button hover:underline">Terms of User</a></label>
                                </div>
                            </div>
                            <div class="block-button mt-6">
                                <button class="button-main bg-primary w-full text-center" type="submit">Create a new account</button>
                            </div>
                            <div class="navigate flex items-center justify-center gap-2 mt-6">
                                <span class="text-surface1">Already have an account?</span>
                                <a class="text-button hover:underline" href="{{ url('/login') }}">Login</a>
                            </div>
                        </form>
                    </div>
                    <div id="employer" class="tab_list {{ $isEmployerPreferred ? 'active' : '' }}" role="tabpanel" aria-labelledby="tab_employer" aria-hidden="{{ $isEmployerPreferred ? 'false' : 'true' }}">
                        <form class="form mt-6" method="POST" action="{{ route('register') }}">
                            @csrf
                            <input type="hidden" name="role" value="employer">
                            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                            <div class="form-group">
                                <label for="name">Full Name*</label>
                                <input id="name" type="text" name="name" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Enter Your Name" required />
                            </div>
                            <div class="form-group mt-6">
                                <label for="username">Employer email address*</label>
                                <input id="username" type="email" name="email" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Email address*" required />
                            </div>
                            <div class="form-group mt-6">
                                <label for="phone_no">Phone Number*</label>
                                <input id="phone_no" type="text" name="phone_no" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Enter Phone Number*" required />
                            </div>
                            <div class="form-group mt-6">
                                <div class="flex flex-col gap-4">
                                    <div>
                                <label for="dob">Date of Birth*</label>
                                <input id="dob" required type="date" name="dob" class="form-control border-line px-4 h-[50px] rounded-lg">
                                    </div>
                                    <div>
                                <label for="gender">Gender*</label>
                                <select id="gender" required name="gender" class="form-control border-line px-8 h-[50px] rounded-lg">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            </div>
                            </div>
                            <div class="form-group mt-6">
                                <label for="password">Password*</label>
                                <input id="password" type="password" name="password" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Password*" required />
                            </div>
                            <div class="form-group mt-6">
                                <label for="confirmPassword">Confirm password*</label>
                                <input id="confirmPassword" type="password" name="password_confirmation" class="form-control w-full mt-3 border border-line px-4 h-[50px] rounded-lg" placeholder="Confirm password*" required />
                            </div>
                            <div class="flex items-center justify-between mt-6">
                                <div class="sub-input-checkbox flex items-center gap-2">
                                    <input id="checkbox" type="checkbox" name="checkbox" required />
                                    <label for="checkbox" class="text-surface1">I agree to the <a href="{{ url('/terms') }}" class="text-button hover:underline">Terms of User</a></label>
                                </div>
                            </div>
                            <div class="block-button mt-6">
                                <button class="button-main bg-primary w-full text-center" type="submit">Create a new account</button>
                            </div>
                            <div class="navigate flex items-center justify-center gap-2 mt-6">
                                <span class="text-surface1">Already have an account?</span>
                                <a class="text-button hover:underline" href="{{ url('/login') }}">Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>


        <button class="scroll-to-top-btn"><span class="ph-bold ph-caret-up"></span></button>
@endsection
