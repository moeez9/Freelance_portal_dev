@extends('layouts.app')

@section('content')
<!-- Hero Section with Search -->
<section class="hero-section bg-surface relative overflow-hidden pt-32 pb-20">
    <div class="container relative z-10">
        <div class="max-w-3xl">
            <h1 class="heading2 mb-6">Find the perfect <span class="text-primary italic">freelance</span> services for your business</h1>

            <form action="{{ route('services.index') }}" method="GET" class="flex items-center bg-white rounded-lg shadow-xl p-2 mb-8">
                <div class="flex-1 flex items-center px-4 border-r border-gray-100">
                    <span class="ph ph-magnifying-glass text-gray-400 text-xl mr-3"></span>
                    <input type="text" name="search" placeholder="What service are you looking for today?" class="w-full border-none focus:ring-0 text-sm py-3">
                </div>
                <div class="hidden md:flex items-center px-4 w-48" x-data="{ open: false }">
                    <select name="category" class="w-full border-none focus:ring-0 text-sm font-bold text-gray-700 cursor-pointer">
                        <option value="">All Categories</option>
                        @foreach($gigCategories ?? [] as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="button-main py-3 px-8 rounded-lg">Search</button>
            </form>

            <div class="flex items-center gap-4 flex-wrap">
                <span class="text-sm font-bold text-gray-500">Popular:</span>
                @foreach($popularTags ?? [] as $tag)
                    <a href="{{ route('services.index', ['search' => $tag]) }}" class="px-4 py-1 border border-gray-300 rounded-full text-xs font-bold hover:bg-gray-100 duration-300">{{ $tag }}</a>
                @endforeach
            </div>
        </div>
    </div>
</section>

@guest
<!-- Guest Jobs & Gigs -->
<section class="py-20 bg-white">
    <div class="container">
        <div class="flex items-center justify-between mb-6">
            <h2 class="heading4">Latest Jobs (Signup as Candidate)</h2>
            <a href="{{ route('register', ['role' => 'candidate']) }}" class="text-primary text-sm font-bold">Join as Candidate</a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($featuredJobs ?? [] as $job)
                <a href="{{ route('register', ['role' => 'candidate', 'redirect_to' => route('jobs.show', $job->slug)]) }}" class="block p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary duration-300">
                    <div class="w-full h-36 rounded-lg overflow-hidden bg-surface mb-3">
                        <img src="{{ $job->upload_logo_url ?? $job->upload_banner_url ?? asset('assets/images/blog/1.webp') }}" alt="{{ $job->title }}" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-title line-clamp-1">{{ $job->title }}</h3>
                    <p class="text-secondary mt-2 text-sm">{{ $job->categories }}</p>
                    <p class="text-secondary mt-1 text-sm">by {{ $job->employer?->name ?? 'Employer' }}</p>
                    <div class="mt-4 text-primary text-sm font-semibold">Signup to Apply</div>
                </a>
            @empty
                <p class="text-secondary col-span-full">No data found</p>
            @endforelse
        </div>

        <div class="flex items-center justify-between mt-14 mb-6">
            <h2 class="heading4">Featured Gigs (Signup as Employer)</h2>
            <a href="{{ route('register', ['role' => 'employer']) }}" class="text-primary text-sm font-bold">Join as Employer</a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($featuredGigs ?? [] as $gig)
                <a href="{{ route('register', ['role' => 'employer', 'redirect_to' => route('services.show', $gig->slug)]) }}" class="block p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary duration-300">
                    <div class="w-full h-36 rounded-lg overflow-hidden bg-surface mb-3">
                        <img src="{{ $gig->thumbnail ? asset('storage/' . $gig->thumbnail) : asset('assets/images/blog/2.webp') }}" alt="{{ $gig->title }}" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-title line-clamp-1">{{ $gig->title }}</h3>
                    <p class="text-secondary mt-2 text-sm">{{ $gig->category ?? 'Service' }}</p>
                    <p class="text-secondary mt-1 text-sm">by {{ $gig->freelancer?->name ?? 'Freelancer' }}</p>
                    <div class="mt-4 text-primary text-sm font-semibold">Signup to Order</div>
                </a>
            @empty
                <p class="text-secondary col-span-full">No data found</p>
            @endforelse
        </div>
    </div>
</section>
@else
<section class="py-20 bg-white">
    <div class="container">
        <div class="flex items-center justify-between mb-6">
            <h2 class="heading4">Latest Jobs</h2>
            <a href="{{ route('jobs.index') }}" class="text-primary text-sm font-bold">View all jobs</a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($featuredJobs ?? [] as $job)
                <a href="{{ route('jobs.show', $job->slug) }}" class="block p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary duration-300">
                    <div class="w-full h-36 rounded-lg overflow-hidden bg-surface mb-3">
                        <img src="{{ $job->upload_logo_url ?? $job->upload_banner_url ?? asset('assets/images/blog/1.webp') }}" alt="{{ $job->title }}" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-title line-clamp-1">{{ $job->title }}</h3>
                    <p class="text-secondary mt-2 text-sm">{{ $job->categories }}</p>
                    <p class="text-secondary mt-1 text-sm">by {{ $job->employer?->name ?? 'Employer' }}</p>
                    <div class="mt-4 text-primary text-sm font-semibold">View Details</div>
                </a>
            @empty
                <p class="text-secondary col-span-full">No data found</p>
            @endforelse
        </div>

        <div class="flex items-center justify-between mt-14 mb-6">
            <h2 class="heading4">Featured Gigs</h2>
            <a href="{{ route('services.index') }}" class="text-primary text-sm font-bold">View all gigs</a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($featuredGigs ?? [] as $gig)
                <a href="{{ route('services.show', $gig->slug) }}" class="block p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary duration-300">
                    <div class="w-full h-36 rounded-lg overflow-hidden bg-surface mb-3">
                        <img src="{{ $gig->thumbnail ? asset('storage/' . $gig->thumbnail) : asset('assets/images/blog/2.webp') }}" alt="{{ $gig->title }}" class="w-full h-full object-cover">
                    </div>
                    <h3 class="font-bold text-title line-clamp-1">{{ $gig->title }}</h3>
                    <p class="text-secondary mt-2 text-sm">{{ $gig->category ?? 'Service' }}</p>
                    <p class="text-secondary mt-1 text-sm">by {{ $gig->freelancer?->name ?? 'Freelancer' }}</p>
                    <div class="mt-4 text-primary text-sm font-semibold">View Details</div>
                </a>
            @empty
                <p class="text-secondary col-span-full">No data found</p>
            @endforelse
        </div>
    </div>
</section>
@endguest

<!-- Categories Section -->
<section class="py-20">
    <div class="container">
        <h2 class="heading3 mb-10">Browse by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($gigCategories ?? [] as $cat)
                <a href="{{ route('services.index', ['category' => $cat->name]) }}" class="group p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md hover:border-primary duration-300 text-center">
                    <span class="ph {{ $cat->icon ?? 'ph-folder' }} text-4xl text-gray-400 group-hover:text-primary duration-300 mb-4 block"></span>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-primary duration-300">{{ $cat->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
