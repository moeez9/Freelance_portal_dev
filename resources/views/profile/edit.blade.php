@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-3xl space-y-6">
        <h3 class="heading3">Profile Setup</h3>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5 bg-white p-6 rounded-lg border border-line">
            @csrf
            @method('PATCH')

            <div class="flex items-center gap-4">
                <img
                    src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : ('https://ui-avatars.com/api/?name=' . urlencode($user->name)) }}"
                    alt="Profile"
                    class="w-16 h-16 rounded-full object-cover border border-line"
                >
                <div class="w-full">
                    <label class="block mb-1">Profile Picture</label>
                    <input type="file" name="profile_pic" accept="image/*" class="w-full border-gray-300 rounded-md">
                    @error('profile_pic')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 rounded-md" required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Email (Not Editable)</label>
                <input type="email" value="{{ $user->email }}" class="w-full border-gray-300 rounded-md bg-gray-100 text-gray-500 cursor-not-allowed" disabled>
            </div>

            <div>
                <label class="block mb-1">Phone Number</label>
                <input type="text" name="phone_no" value="{{ old('phone_no', $user->phone_no) }}" class="w-full border-gray-300 rounded-md" required>
                @error('phone_no')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if($user->role === 'candidate')
                <div class="pt-2 border-t border-line">
                    <h5 class="heading5 mb-4">Payment Method Setup</h5>
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-1">Payment Method</label>
                            <select name="candidate_payment_method" class="w-full border-gray-300 rounded-md">
                                <option value="">Select Method</option>
                                <option value="bank_transfer" @selected(old('candidate_payment_method', $user->candidate_payment_method) === 'bank_transfer')>Bank Transfer</option>
                                <option value="jazzcash" @selected(old('candidate_payment_method', $user->candidate_payment_method) === 'jazzcash')>JazzCash</option>
                                <option value="easypaisa" @selected(old('candidate_payment_method', $user->candidate_payment_method) === 'easypaisa')>EasyPaisa</option>
                            </select>
                            @error('candidate_payment_method')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-1">Payment Details</label>
                            <textarea name="candidate_payment_details" rows="4" class="w-full border-gray-300 rounded-md" placeholder="Account title, account number / IBAN / wallet number">{{ old('candidate_payment_details', $user->candidate_payment_details) }}</textarea>
                            @error('candidate_payment_details')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <button class="button-main">Save Profile</button>
        </form>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4 bg-white p-6 rounded-lg border border-line">
            @csrf
            @method('PUT')
            <h5 class="heading5">Create / Change Password</h5>
            <div>
                <label class="block mb-1">Current Password</label>
                <input type="password" name="current_password" class="w-full border-gray-300 rounded-md" required>
                @error('current_password', 'updatePassword')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block mb-1">New Password</label>
                <input type="password" name="password" class="w-full border-gray-300 rounded-md" required>
                @error('password', 'updatePassword')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-md" required>
            </div>
            <button class="button-main">Update Password</button>
        </form>

        <form method="POST" action="{{ route('profile.destroy') }}" class="bg-white p-6 rounded-lg border border-line">
            @csrf
            @method('DELETE')
            <h5 class="heading5 mb-4 text-red-600">Delete Account</h5>
            <label class="block mb-1">Confirm Password</label>
            <input type="password" name="password" class="w-full border-gray-300 rounded-md" required>
            <button class="button-main mt-4 bg-red-600 hover:bg-red-700">Delete Account</button>
        </form>
    </div>
</section>
@endsection
