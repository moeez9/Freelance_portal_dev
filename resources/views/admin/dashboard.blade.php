@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-4xl">
        <div class="bg-white rounded-lg border border-line p-6">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-4">
                    <img
                        src="{{ !empty($profile?->profile_pic) ? asset('storage/' . $profile->profile_pic) : ('https://ui-avatars.com/api/?name=' . urlencode($profile?->name ?? $email)) }}"
                        alt="Admin"
                        class="w-14 h-14 rounded-full object-cover border border-line"
                    >
                    <div>
                        <h4 class="heading4">{{ $profile?->name ?? 'Admin' }}</h4>
                        <p class="text-secondary text-sm">{{ $email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="button-main -border">Logout</button>
                </form>
            </div>

            <div class="grid md:grid-cols-2 gap-4 mt-6">
                <a href="{{ route('admin.users.index') }}" class="block p-5 rounded-lg border border-line hover:bg-surface duration-200">
                    <strong class="text-title">Users, Roles & Posts</strong>
                    <p class="text-secondary text-sm mt-1">View candidate/employer registrations with posted gigs and jobs.</p>
                </a>
                <a href="{{ route('admin.demo.payments') }}" class="block p-5 rounded-lg border border-line hover:bg-surface duration-200">
                    <strong class="text-title">Demo Payments Panel</strong>
                    <p class="text-secondary text-sm mt-1">View pending/verified payments and verify manually.</p>
                </a>
                <a href="{{ route('admin.profile.setup') }}" class="block p-5 rounded-lg border border-line hover:bg-surface duration-200">
                    <strong class="text-title">Edit Admin Profile</strong>
                    <p class="text-secondary text-sm mt-1">Update admin name and profile picture.</p>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
