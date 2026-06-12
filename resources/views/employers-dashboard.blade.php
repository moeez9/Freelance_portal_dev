@extends('layouts.app')

@section('content')
<div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex sm:pt-20 pt-16">
    <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
        @include('partials.dashboard.employer-menu', ['active' => 'dashboard'])
    </div>

    <div class="content_dashboard scrollbar_custom max-h-full w-full h-fit bg-surface">
        <div class="container h-full lg:py-15 sm:py-12 py-8">
            <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden" data-type="menu_dashboard">
                <span class="ph ph-squares-four text-xl"></span>
                <strong class="text-button">Menu</strong>
            </button>

            <h4 class="heading4 max-lg:mt-3">Employer Dashboard</h4>

            <ul class="list_counter grid 2xl:grid-cols-5 grid-cols-2 sm:gap-7.5 gap-5 mt-7.5 w-full">
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">Posted Jobs</span>
                        <h4 class="number heading4 mt-1">{{ $stats['posted_jobs'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-briefcase sm:text-3xl text-2xl text-white"></span>
                    </div>
                </li>
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">Open Jobs</span>
                        <h4 class="number heading4 mt-1">{{ $stats['open_jobs'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-lock-open sm:text-3xl text-2xl text-white"></span>
                    </div>
                </li>
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">Closed Jobs</span>
                        <h4 class="number heading4 mt-1">{{ $stats['closed_jobs'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-lock sm:text-3xl text-2xl text-white"></span>
                    </div>
                </li>
                <li class="counter_item flex items-center justify-between sm:gap-4 gap-3 sm:p-6 p-5 rounded-lg bg-white">
                    <div class="counter_content">
                        <span class="text-secondary">Applications</span>
                        <h4 class="number heading4 mt-1">{{ $stats['total_applications'] }}</h4>
                    </div>
                    <div class="counter_icon flex items-center justify-center sm:w-[72px] w-12 sm:h-[72px] h-12 rounded-full bg-gradient">
                        <span class="ph-fill ph-users sm:text-3xl text-2xl text-white"></span>
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
                    <h5 class="heading5 mb-4">Monthly Performance (Last 6 Months)</h5>
                    <div id="employer-dashboard-chart"></div>
                </div>

                <div class="rounded-lg bg-white p-6">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <h5 class="heading5">Recent Jobs</h5>
                        <a href="{{ route('employer.jobs.index') }}" class="text-primary underline">View all</a>
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
                                @forelse($recentJobs as $job)
                                    <tr class="border-b border-line/60">
                                        <td class="py-3 pr-3">{{ $job->title }}</td>
                                        <td class="py-3 pr-3">{{ $job->categories }}</td>
                                        <td class="py-3 pr-3 capitalize">{{ $job->status }}</td>
                                        <td class="py-3 pr-3 whitespace-nowrap">{{ $job->created_at?->format('M d, Y') }}</td>
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
                        <h5 class="heading5">Recent Applicants</h5>
                        <a href="{{ route('employer.applicants') }}" class="text-primary underline">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-line">
                                <tr>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Freelancer</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Job</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Bid</th>
                                    <th class="text-left py-3 text-sm uppercase text-secondary">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentApplicants as $proposal)
                                    <tr class="border-b border-line/60">
                                        <td class="py-3 pr-3">{{ $proposal->freelancer?->name ?? 'N/A' }}</td>
                                        <td class="py-3 pr-3">{{ $proposal->job?->title ?? 'N/A' }}</td>
                                        <td class="py-3 pr-3">${{ number_format((float) $proposal->bid_amount, 2) }}</td>
                                        <td class="py-3 pr-3 capitalize">{{ $proposal->status }}</td>
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
        @include('partials.dashboard.employer-menu', ['active' => 'dashboard'])
    </div>
</div>
@endsection

@push('scripts')
<script src="https://freelanhub.vercel.app/assets/js/apexcharts.js"></script>
<script>
    (function () {
        const chartEl = document.querySelector('#employer-dashboard-chart');
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
                    name: 'Posted Jobs',
                    data: @json($jobsSeries)
                },
                {
                    name: 'Applications',
                    data: @json($applicantsSeries)
                }
            ],
            xaxis: {
                categories: @json($chartLabels)
            },
            colors: ['#04b2b2', '#0ea5e9'],
            yaxis: {
                min: 0,
                forceNiceScale: true
            }
        };

        new ApexCharts(chartEl, options).render();
    })();
</script>
@endpush
