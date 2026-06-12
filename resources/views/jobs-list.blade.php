@extends('layouts.app')

@section('content')
<section class="breadcrumb">
    <div class="breadcrumb_inner relative lg:py-20 py-14">
        <div class="breadcrumb_bg absolute top-0 left-0 w-full h-full">
            <img src="https://freelanhub.vercel.app/assets/images/components/breadcrumb_job.webp" alt="breadcrumb_job" class="w-full h-full object-cover" />
        </div>
        <div class="container relative h-full">
            <div class="breadcrumb_content flex flex-col items-start justify-center w-full h-full">
                <h3 class="heading3 text-white mt-2">Jobs List</h3>
                <form class="mt-5 flex gap-3 w-full max-w-3xl" method="GET" action="{{ route('jobs.index') }}">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search jobs" class="w-full h-12 rounded px-4">
                    <button type="submit" class="button-main">Search</button>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="jobs lg:py-20 sm:py-14 py-10">
    <div class="container">
        <div class="mb-4 text-secondary">{{ $jobs->total() }} jobs found</div>
        <ul class="list_jobs overflow-hidden w-full rounded-lg bg-white shadow-lg">
            @forelse($jobs as $job)
                <li class="item border-b border-line">
                    <div class="jobs_item -style-list flex flex-wrap items-center justify-between gap-4 sm:px-6 px-5 py-4">
                        <a href="{{ route('jobs.show', $job->slug) }}" class="jobs_info flex flex-wrap items-center gap-3">
                            <img src="{{ $job->upload_logo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($job->employer->name)) }}" alt="company" class="jobs_logo w-15 h-15 flex-shrink-0" />
                            <div>
                                <span class="jobs_company text-sm font-semibold text-primary">{{ $job->employer->name }}</span>
                                <strong class="jobs_name max-sm:mt-0.5 text-title block">{{ $job->title }}</strong>
                                <div class="mt-1 flex items-center gap-2 flex-wrap">
                                    @if($job->status === 'open')
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700 font-semibold">Open</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 font-semibold">Closed</span>
                                        @if($job->closed_by_employer)
                                            <span class="text-xs text-secondary">Closed by Employer</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </a>
                        <div class="jobs_tag flex flex-wrap items-center gap-2">
                            <span class="caption1 tag bg-surface">{{ $job->categories }}</span>
                        </div>
                        <div class="jobs_price">
                            <span class="price text-title">${{ $job->min }} - ${{ $job->max }}</span>
                            <span class="text-secondary">/{{ $job->salary_type }}</span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-10 text-center text-gray-500">No jobs found.</li>
            @endforelse
        </ul>
        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection
