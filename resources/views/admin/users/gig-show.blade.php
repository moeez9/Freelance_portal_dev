@extends('layouts.app')

@section('content')
<section class="pt-24 sm:pt-28 pb-12 sm:pb-16 bg-surface min-h-screen">
    <div class="container max-w-5xl">
        <div class="bg-white rounded-lg border border-line p-4 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h4 class="heading4 break-words">{{ $gig->title }}</h4>
                    <p class="text-sm text-secondary mt-1">Candidate: {{ $user->name }}</p>
                </div>
                <a href="{{ route('admin.users.show', $user) }}" class="button-main -border w-full sm:w-auto text-center">Back</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5">
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Status</p>
                    <p class="font-semibold capitalize">{{ $gig->status }}</p>
                </div>
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Category</p>
                    <p class="font-semibold">{{ $gig->category ?? 'N/A' }}</p>
                </div>
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Subcategory</p>
                    <p class="font-semibold">{{ $gig->sub_category ?? 'N/A' }}</p>
                </div>
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Service Type</p>
                    <p class="font-semibold">{{ $gig->service_type ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h5 class="heading5">Description</h5>
                <p class="text-secondary mt-2 whitespace-pre-line">{{ $gig->description }}</p>
            </div>

            <div class="mt-6">
                <h5 class="heading5 mb-3">Packages</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @forelse($gig->packages as $package)
                        <div class="border border-line rounded-lg p-4">
                            <p class="font-semibold capitalize">{{ $package->type }} - {{ $package->name }}</p>
                            <p class="text-sm text-secondary mt-1">{{ $package->description }}</p>
                            <p class="mt-2 text-sm">Price: <strong>${{ number_format((float) $package->price, 2) }}</strong></p>
                            <p class="text-sm">Delivery: <strong>{{ $package->delivery_days }} days</strong></p>
                            <p class="text-sm">Revisions: <strong>{{ $package->revisions }}</strong></p>
                        </div>
                    @empty
                        <p class="text-secondary">No package found.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                <h5 class="heading5 mb-3">Requirements</h5>
                <ul class="space-y-2">
                    @forelse($gig->requirements as $requirement)
                        <li class="border border-line rounded-lg p-3 text-sm">{{ $requirement->question }}</li>
                    @empty
                        <li class="text-secondary text-sm">No requirements added.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
