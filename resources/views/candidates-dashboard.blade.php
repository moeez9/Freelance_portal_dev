@extends('layouts.app')

@section('content')
<div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex">
    <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
        @include('partials.dashboard.candidate-menu', ['active' => 'dashboard'])
    </div>

    <div class="content_dashboard scrollbar_custom max-h-full w-full h-fit bg-surface">
        <div class="container h-full lg:py-15 sm:py-12 py-8">
            <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden" data-type="menu_dashboard">
                <span class="ph ph-squares-four text-xl"></span>
                <strong class="text-button">Menu</strong>
            </button>

            <h4 class="heading4 max-lg:mt-3">Candidate Dashboard</h4>

            <ul class="list_counter grid 2xl:grid-cols-5 grid-cols-2 sm:gap-7.5 gap-5 mt-7.5 w-full">
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">Applied Jobs</span>
                        <h4 class="number heading4 mt-1">{{ $stats['applied_jobs'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-briefcase sm:text-3xl text-2xl text-white"></span>
                    </div>
                </li>
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">Active Proposals</span>
                        <h4 class="number heading4 mt-1">{{ $stats['active_proposals'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-clock sm:text-3xl text-2xl text-white"></span>
                    </div>
                </li>
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">Accepted Proposals</span>
                        <h4 class="number heading4 mt-1">{{ $stats['accepted_proposals'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-check-circle sm:text-3xl text-2xl text-white"></span>
                    </div>
                </li>
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">My Services</span>
                        <h4 class="number heading4 mt-1">{{ $stats['total_services'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-notepad sm:text-3xl text-2xl text-white"></span>
                    </div>
                </li>
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">Total Reviews</span>
                        <h4 class="number heading4 mt-1">{{ $stats['total_reviews'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-star sm:text-3xl text-2xl text-white"></span>
                    </div>
                </li>
            </ul>

            <div class="grid xl:grid-cols-2 gap-7.5 mt-7.5">
                <div class="rounded-lg bg-white p-6 xl:col-span-2">
                    <h5 class="heading5 mb-4">Monthly Activity (Last 6 Months)</h5>
                    <div id="candidate-dashboard-chart"></div>
                </div>

                <div class="rounded-lg bg-white p-6">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <h5 class="heading5">Recent Proposals</h5>
                        <a href="{{ route('candidate.proposals') }}" class="text-primary underline">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-line">
                                <tr>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Job</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Bid</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Status</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProposals as $proposal)
                                    <tr class="border-b border-line/60">
                                        <td class="py-3 pr-3">{{ $proposal->job?->title ?? 'N/A' }}</td>
                                        <td class="py-3 pr-3">${{ number_format((float) $proposal->bid_amount, 2) }}</td>
                                        <td class="py-3 pr-3 capitalize">{{ $proposal->status }}</td>
                                        <td class="py-3 pr-3 whitespace-nowrap">{{ $proposal->created_at?->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-5 text-secondary text-center">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <h5 class="heading5">Recent Services</h5>
                        <a href="{{ route('candidate.services') }}" class="text-primary underline">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-line">
                                <tr>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Title</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Category</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Status</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentServices as $service)
                                    <tr class="border-b border-line/60">
                                        <td class="py-3 pr-3">{{ $service->title }}</td>
                                        <td class="py-3 pr-3">{{ $service->category ?? 'N/A' }}</td>
                                        <td class="py-3 pr-3 capitalize">{{ $service->status }}</td>
                                        <td class="py-3 pr-3 whitespace-nowrap">{{ $service->created_at?->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-5 text-secondary text-center">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal">
    <div class="modal_item menu_dashboard -modal overflow-hidden relative flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white" data-type="menu_dashboard">
        @include('partials.dashboard.candidate-menu', ['active' => 'dashboard'])
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/apexcharts.js') }}"></script>
<script>
    (function () {
        const chartEl = document.querySelector('#candidate-dashboard-chart');
        if (!chartEl || typeof ApexCharts === 'undefined') {
            return;
        }

        const options = {
            chart: {
                type: 'line',
                height: 320,
                toolbar: { show: false }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            series: [
                {
                    name: 'Proposals',
                    data: @json($proposalSeries)
                },
                {
                    name: 'Services',
                    data: @json($serviceSeries)
                }
            ],
            xaxis: {
                categories: @json($chartLabels)
            },
            colors: ['#04b2b2', '#1f2937'],
            yaxis: {
                min: 0,
                forceNiceScale: true
            }
        };

        new ApexCharts(chartEl, options).render();
    })();
</script>
@endpush
