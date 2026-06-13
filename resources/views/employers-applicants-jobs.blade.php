@extends('layouts.app')

@section('content')
<div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex">
    <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
        @include('partials.dashboard.employer-menu', ['active' => 'applicants'])
    </div>

    <div class="dashboard_alert scrollbar_custom w-full bg-surface">
        <div class="container h-fit lg:pt-15 lg:pb-30 max-lg:py-12 max-sm:py-8">
            <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden" data-type="menu_dashboard">
                <span class="ph ph-squares-four text-xl"></span>
                <strong class="text-button">Menu</strong>
            </button>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <h4 class="heading4 max-lg:mt-3">Applicants Jobs</h4>
                <p class="text-secondary">Total Applications: {{ $proposals->total() }}</p>
            </div>

            <div class="mt-7.5 rounded-lg bg-white">
                @if(request('status'))
                    <div class="px-6 pt-5">
                        <div class="rounded-lg border border-line bg-surface px-4 py-3 text-secondary">
                            Showing applications with <strong class="text-title capitalize">{{ request('status') }}</strong> status.
                        </div>
                    </div>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-5 pt-5 px-6">
                    <form class="relative w-[340px] h-12" method="GET" action="{{ route('employer.applicants') }}">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="w-full h-full pl-4 pr-12 border border-line rounded-lg overflow-hidden"
                            placeholder="Search by freelancer or job title"
                        />
                        <button type="submit" class="absolute top-1/2 -translate-y-1/2 right-4">
                            <span class="ph ph-magnifying-glass text-xl block"></span>
                        </button>
                    </form>

                    <form method="GET" action="{{ route('employer.applicants') }}" class="flex items-center gap-3">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="status" class="h-12 px-4 border border-line rounded-lg bg-white">
                            <option value="">All Statuses</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="accepted" @selected(request('status') === 'accepted')>Accepted</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                        </select>
                        <button type="submit" class="button-main h-12">Filter</button>
                    </form>
                </div>

                @if(isset($selectedProposal) && $selectedProposal)
                    <div class="px-6 mt-5">
                        <div class="rounded-lg border border-line bg-surface p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h6 class="heading6">Proposal Detail</h6>
                                    <p class="text-secondary mt-1">
                                        {{ $selectedProposal->freelancer?->name ?? 'Freelancer' }} applied on "{{ $selectedProposal->job?->title ?? 'N/A' }}"
                                    </p>
                                </div>
                                <span class="tag bg-opacity-10 bg-yellow text-yellow text-button capitalize">{{ $selectedProposal->status }}</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4 mt-4 text-sm">
                                <div class="rounded border border-line bg-white p-3">
                                    <p class="text-secondary">Bid Amount</p>
                                    <p class="text-title font-bold mt-1">${{ number_format((float) $selectedProposal->bid_amount, 2) }}</p>
                                </div>
                                <div class="rounded border border-line bg-white p-3">
                                    <p class="text-secondary">Applied Date</p>
                                    <p class="text-title font-bold mt-1">{{ $selectedProposal->created_at?->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                            <div class="rounded border border-line bg-white p-4 mt-4">
                                <p class="text-secondary text-sm mb-2">Cover Letter / Proposal</p>
                                <p class="text-title whitespace-pre-line">{{ $selectedProposal->cover_letter ?: 'No proposal text provided.' }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 mt-4">
                                @if($selectedProposal->cv_file_url)
                                    <a href="{{ $selectedProposal->cv_file_url }}" target="_blank" class="button-main bg-slate-600 hover:bg-slate-700">View CV (PDF)</a>
                                @endif
                                <form method="POST" action="{{ route('conversations.store') }}">
                                    @csrf
                                    <input type="hidden" name="context_type" value="proposal">
                                    <input type="hidden" name="context_id" value="{{ $selectedProposal->id }}">
                                    <button type="submit" class="button-main">Message Candidate</button>
                                </form>
                                <a href="{{ route('employer.applicants', request()->except('proposal')) }}" class="h-12 px-5 inline-flex items-center rounded border border-line bg-white text-title">
                                    Close Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="list overflow-x-auto w-full p-5 rounded-xl">
                    <table class="w-full max-[1400px]:w-[1200px] max-md:w-[1000px]">
                        <thead class="border-b border-line">
                            <tr>
                                <th scope="col" class="px-5 py-4 text-left text-sm font-bold uppercase text-secondary whitespace-nowrap">Freelancer</th>
                                <th scope="col" class="px-5 py-4 text-left text-sm font-bold uppercase text-secondary whitespace-nowrap">Job Title</th>
                                <th scope="col" class="px-5 py-4 text-left text-sm font-bold uppercase text-secondary whitespace-nowrap">Bid Amount</th>
                                <th scope="col" class="px-5 py-4 text-left text-sm font-bold uppercase text-secondary whitespace-nowrap">Date Applied</th>
                                <th scope="col" class="px-5 py-4 text-left text-sm font-bold uppercase text-secondary whitespace-nowrap">Status</th>
                                <th scope="col" class="px-5 py-4 text-right text-sm font-bold uppercase text-secondary whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proposals as $proposal)
                                @php
                                    $freelancer = $proposal->freelancer;
                                    $job = $proposal->job;
                                    $profilePic = $freelancer?->profile_pic;
                                    $avatarUrl = $profilePic
                                        ? (str_starts_with($profilePic, 'http') ? $profilePic : asset('storage/' . ltrim($profilePic, '/')))
                                        : null;

                                    $statusStyle = match ($proposal->status) {
                                        'accepted' => 'bg-opacity-10 bg-success text-success',
                                        'rejected' => 'bg-opacity-10 bg-red text-red',
                                        default => 'bg-opacity-10 bg-yellow text-yellow',
                                    };
                                @endphp
                                <tr class="item duration-300 hover:bg-background">
                                    <th scope="row" class="p-5 text-left">
                                        <div class="info flex items-center gap-3">
                                            <span class="avatar flex-shrink-0 w-15 h-15 rounded-full overflow-hidden bg-surface border border-line">
                                                @if($avatarUrl)
                                                    <img src="{{ $avatarUrl }}" alt="{{ $freelancer?->name ?? 'Freelancer' }}" class="w-full h-full object-cover" />
                                                @endif
                                            </span>
                                            <div>
                                                <strong class="candidates_name -style-1 heading6">{{ $freelancer?->name ?? 'Unknown Freelancer' }}</strong>
                                                <div class="address flex items-center gap-2 mt-1 text-secondary">
                                                    <span class="ph ph-envelope-simple text-lg"></span>
                                                    <span class="employers_address font-normal">{{ $freelancer?->email ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="p-5 heading6">{{ $job?->title ?? 'N/A' }}</td>
                                    <td class="p-5 whitespace-nowrap">${{ number_format((float) $proposal->bid_amount, 2) }}</td>
                                    <td class="p-5 whitespace-nowrap">{{ $proposal->created_at?->format('M d, Y') }}</td>
                                    <td class="p-5">
                                        <span class="tag {{ $statusStyle }} text-button capitalize">{{ $proposal->status }}</span>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex justify-end gap-2">
                                            @if($proposal->status === 'pending')
                                                <form method="POST" action="{{ route('proposals.accept', $proposal) }}">
                                                    @csrf
                                                    <button type="submit" class="btn_action flex items-center justify-center relative w-10 h-10 rounded border border-line duration-300 hover:bg-primary hover:text-white" title="Accept">
                                                        <span class="ph ph-check text-xl"></span>
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('proposals.reject', $proposal) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn_action flex items-center justify-center relative w-10 h-10 rounded border border-line duration-300 hover:bg-red hover:text-white" title="Reject">
                                                        <span class="ph ph-x text-xl"></span>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('employer.applicants', array_merge(request()->query(), ['proposal' => $proposal->slug])) }}" class="btn_action flex items-center justify-center relative w-10 h-10 rounded border border-line duration-300 hover:bg-primary hover:text-white" title="View Proposal">
                                                <span class="ph ph-eye text-xl"></span>
                                            </a>
                                            @if($proposal->cv_file_url)
                                                <a href="{{ $proposal->cv_file_url }}" target="_blank" class="btn_action flex items-center justify-center relative w-10 h-10 rounded border border-line duration-300 hover:bg-primary hover:text-white" title="View CV">
                                                    <span class="ph ph-file-pdf text-xl"></span>
                                                </a>
                                            @endif

                                            <form method="POST" action="{{ route('conversations.store') }}">
                                                @csrf
                                                <input type="hidden" name="context_type" value="proposal">
                                                <input type="hidden" name="context_id" value="{{ $proposal->id }}">
                                                <button type="submit" class="btn_action flex items-center justify-center relative w-10 h-10 rounded border border-line duration-300 hover:bg-primary hover:text-white" title="Message">
                                                    <span class="ph ph-chat-circle-text text-xl"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-secondary">
                                        No applications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 p-6 border-t border-line">
                    <p class="text-secondary whitespace-nowrap">
                        Showing {{ $proposals->firstItem() ?? 0 }} to {{ $proposals->lastItem() ?? 0 }} of {{ $proposals->total() }} entries
                    </p>
                    <div>
                        {{ $proposals->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal">
    <div class="modal_item menu_dashboard -modal overflow-hidden relative flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white" data-type="menu_dashboard">
        @include('partials.dashboard.employer-menu', ['active' => 'applicants'])
    </div>
</div>
@endsection
