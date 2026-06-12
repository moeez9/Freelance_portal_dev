@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-xl">
        <div class="bg-white p-6 rounded-lg border border-line">
            <h4 class="heading4 mb-3">Verify Email</h4>
            <p class="text-secondary mb-4">Thanks for signing up. Verify your email to continue.</p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="button-main">Resend Verification Email</button>
            </form>
        </div>
    </div>
</section>
@endsection
