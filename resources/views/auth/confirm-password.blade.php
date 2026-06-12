@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-xl">
        <form method="POST" action="{{ route('password.confirm') }}" class="bg-white p-6 rounded-lg border border-line space-y-4">
            @csrf
            <h4 class="heading4">Confirm Password</h4>
            <input type="password" name="password" required class="w-full border-gray-300 rounded-md" placeholder="Password">
            <button class="button-main">Confirm</button>
        </form>
    </div>
</section>
@endsection
