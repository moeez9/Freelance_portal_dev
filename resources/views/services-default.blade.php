@extends('layouts.app')

@section('content')
<section class="breadcrumb">
    <div class="breadcrumb_inner relative lg:py-20 py-14">
        <div class="breadcrumb_bg absolute top-0 left-0 w-full h-full">
            <img src="{{ asset('assets/images/components/breadcrumb_service.webp') }}" alt="breadcrumb_service" class="w-full h-full object-cover" />
        </div>
        <div class="container relative h-full">
            <div class="breadcrumb_content flex flex-col items-start justify-center xl:w-[1000px] lg:w-[848px] md:w-5/6 w-full h-full">
                <div class="list_breadcrumb flex items-center gap-2 animate animate_top" style="--i: 1">
                    <a href="{{ url('/') }}" class="caption1 text-white">Home</a>
                    <span class="caption1 text-white opacity-40">/</span>
                    <span class="caption1 text-white">Services</span>
                </div>
                <h3 class="heading3 text-white mt-2 animate animate_top" style="--i: 2">Browse All Gigs</h3>
            </div>
        </div>
    </div>
</section>

<div class="services lg:py-20 sm:py-14 py-10">
    <div class="container flex flex-col items-center">
        <div class="list_filtered flex flex-wrap items-center gap-3 w-full mt-5">
            <span class="quantity pr-3 border-r border-line">{{ $gigs->count() }} Results</span>
        </div>
        
        <ul class="list_layout_cols list_services grid xl:grid-cols-4 sm:grid-cols-2 md:gap-7.5 gap-5 w-full md:mt-10 mt-7">
            @forelse($gigs as $gig)
                <li class="item h-full">
                    <div class="service_item overflow-hidden relative h-full rounded-lg bg-white shadow-md duration-300 hover:shadow-xl">
                        <a href="{{ route('services.show', $gig->slug) }}" class="service_thumb">
                            <img src="{{ $gig->thumbnail ? asset('storage/' . $gig->thumbnail) : asset('assets/images/service/1.webp') }}" alt="thumbnail" class="w-full h-48 object-cover" />
                        </a>
                        <div class="service_info py-5 px-4">
                            <div class="flex items-center justify-between">
                                <span class="tag caption2 bg-surface">{{ $gig->category }}</span>
                            </div>
                            <a href="{{ route('services.show', $gig->slug) }}" class="service_title text-title pt-2 duration-300 hover:text-primary block h-12 overflow-hidden">
                                {{ $gig->title }}
                            </a>
                            @php
                                $packageMap = $gig->packages->keyBy('type');
                            @endphp
                            <div class="mt-3 space-y-1 text-xs text-gray-600">
                                <div>Basic: ${{ $packageMap['basic']->price ?? '-' }} / {{ $packageMap['basic']->delivery_days ?? ($packageMap['basic']->delivery_time ?? '-') }}d</div>
                                <div>Standard: ${{ $packageMap['standard']->price ?? '-' }} / {{ $packageMap['standard']->delivery_days ?? ($packageMap['standard']->delivery_time ?? '-') }}d</div>
                                <div>Premium: ${{ $packageMap['premium']->price ?? '-' }} / {{ $packageMap['premium']->delivery_days ?? ($packageMap['premium']->delivery_time ?? '-') }}d</div>
                            </div>
                            <div class="service_more_info flex items-center justify-between gap-1 mt-4 pt-4 border-t border-line">
                                <div class="service_author flex items-center gap-2">
                                    <span class="service_author_name -style-1">{{ $gig->freelancer->name }}</span>
                                </div>
                                <div class="service_price whitespace-nowrap">
                                    <span class="text-secondary">From </span>
                                    <span class="price text-title">${{ $gig->packages->min('price') ?? '0' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <p class="text-center w-full">No gigs found.</p>
            @endforelse
        </ul>
    </div>
</div>
@endsection
