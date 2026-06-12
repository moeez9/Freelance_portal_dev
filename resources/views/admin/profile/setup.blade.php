@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-2xl">
        <form method="POST" action="{{ route('admin.profile.setup.save') }}" enctype="multipart/form-data" class="bg-white p-6 rounded-lg border border-line space-y-5">
            @csrf
            <h4 class="heading4">Admin Profile Setup</h4>
            <p class="text-sm text-secondary">Before admin operations, please set your name and profile picture.</p>

            <div>
                <label class="block mb-1">Admin Email</label>
                <input type="email" value="{{ $email }}" disabled class="w-full border-gray-300 rounded-md bg-gray-100 text-gray-500">
            </div>

            <div>
                <label class="block mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $profile->name ?? '') }}" required class="w-full border-gray-300 rounded-md" placeholder="Enter admin name">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Profile Picture</label>
                <input type="file" name="profile_pic" accept="image/*" class="w-full border-gray-300 rounded-md">
                @error('profile_pic')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button class="button-main">Save and Continue</button>
        </form>
    </div>
</section>
@endsection
