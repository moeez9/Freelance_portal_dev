@extends('layouts.app')

@section('content')
<section class="pt-24 sm:pt-28 pb-12 sm:pb-16 bg-surface min-h-screen">
    <div class="container max-w-5xl">
        <div class="bg-white rounded-lg border border-line p-4 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h4 class="heading4 break-words">{{ $job->title }}</h4>
                    <p class="text-sm text-secondary mt-1">Employee/Client: {{ $user->name }}</p>
                </div>
                <a href="{{ route('admin.users.show', $user) }}" class="button-main -border w-full sm:w-auto text-center">Back</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5">
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Status</p>
                    <p class="font-semibold capitalize">
                        {{ $job->status }}
                        @if($job->status === 'closed' && $job->closed_by_employer)
                            (Closed by Employer)
                        @endif
                    </p>
                </div>
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Category</p>
                    <p class="font-semibold">{{ $job->categories ?? 'N/A' }}</p>
                </div>
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Salary</p>
                    <p class="font-semibold">${{ number_format((float) $job->min, 2) }} - ${{ number_format((float) $job->max, 2) }}</p>
                </div>
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Deadline</p>
                    <p class="font-semibold">{{ $job->deadline ? \Illuminate\Support\Carbon::parse($job->deadline)->format('M d, Y') : 'N/A' }}</p>
                </div>
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Contact Email</p>
                    <p class="font-semibold break-all">{{ $job->email ?? 'N/A' }}</p>
                </div>
                <div class="border border-line rounded-lg p-3">
                    <p class="text-xs uppercase text-secondary">Contact Phone</p>
                    <p class="font-semibold">{{ $job->phone_no ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-5">
                <h5 class="heading5">Description</h5>
                <p class="text-secondary mt-2 whitespace-pre-line">{{ $job->description }}</p>
            </div>
            <div class="mt-5">
                <h5 class="heading5">Requirements</h5>
                <p class="text-secondary mt-2 whitespace-pre-line">{{ $job->requirements ?? 'N/A' }}</p>
            </div>
            <div class="mt-5">
                <h5 class="heading5">Required Skills</h5>
                <p class="text-secondary mt-2 whitespace-pre-line">{{ $job->required_skills ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</section>
@endsection
