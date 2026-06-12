@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-xl">
        <form method="POST" action="{{ route('admin.login.submit') }}" class="bg-white p-6 rounded-lg border border-line space-y-4">
            @csrf
            <h4 class="heading4">Admin Login</h4>
            <p class="text-sm text-secondary">Only configured admins can login. Signup is not required.</p>

            <div>
                <label class="block mb-1">Admin Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-gray-300 rounded-md" placeholder="admin@example.com">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Password</label>
                <input type="password" name="password" required class="w-full border-gray-300 rounded-md" placeholder="Enter password">
            </div>

            <button class="button-main">Login as Admin</button>
        </form>
    </div>
</section>
@endsection
