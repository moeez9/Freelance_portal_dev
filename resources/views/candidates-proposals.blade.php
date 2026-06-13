@extends('layouts.app')

@section('content')
<div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex">
    <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
        @include('partials.dashboard.candidate-menu', ['active' => 'proposals'])
    </div>

    <div class="dashboard_proposal scrollbar_custom w-full bg-surface">
        <div class="container h-fit lg:pt-15 lg:pb-30 max-lg:py-12 max-sm:py-8">
            <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden" data-type="menu_dashboard">
                <span class="ph ph-squares-four text-xl"></span>
                <strong class="text-button">Menu</strong>
            </button>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <h4 class="heading4 max-lg:mt-3">My Proposals</h4>
                <p class="text-secondary">Total: {{ $proposals->count() }}</p>
            </div>

            <div class="mt-7.5 rounded-lg bg-white p-5 overflow-x-auto">
                <table class="w-full max-[1200px]:w-[1050px]">
                    <thead class="border-b border-line">
                        <tr>
                            <th class="px-4 py-4 text-left text-sm font-bold uppercase text-secondary">Job</th>
                            <th class="px-4 py-4 text-left text-sm font-bold uppercase text-secondary">Bid</th>
                            <th class="px-4 py-4 text-left text-sm font-bold uppercase text-secondary">CV</th>
                            <th class="px-4 py-4 text-left text-sm font-bold uppercase text-secondary">Date</th>
                            <th class="px-4 py-4 text-left text-sm font-bold uppercase text-secondary">Status</th>
                            <th class="px-4 py-4 text-right text-sm font-bold uppercase text-secondary">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proposals as $proposal)
                            @php
                                $statusStyle = match ($proposal->status) {
                                    'accepted' => 'bg-opacity-10 bg-success text-success',
                                    'rejected' => 'bg-opacity-10 bg-red text-red',
                                    default => 'bg-opacity-10 bg-yellow text-yellow',
                                };
                            @endphp
                            <tr class="border-b border-line align-top">
                                <td class="px-4 py-4">
                                    <a href="{{ route('jobs.show', $proposal->job) }}" class="font-bold text-title hover:underline">{{ $proposal->job?->title ?? 'N/A' }}</a>
                                    <div class="text-xs text-secondary mt-1">{{ $proposal->job?->employer?->name ?? 'Employer' }}</div>
                                    <div class="text-xs text-secondary mt-1 whitespace-pre-line line-clamp-2">{{ $proposal->cover_letter }}</div>
                                </td>
                                <td class="px-4 py-4">${{ number_format((float) $proposal->bid_amount, 2) }}</td>
                                <td class="px-4 py-4">
                                    @if($proposal->cv_file_url)
                                        <a href="{{ $proposal->cv_file_url }}" target="_blank" class="text-primary underline">View PDF</a>
                                    @else
                                        <span class="text-secondary">No file</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">{{ $proposal->created_at?->format('M d, Y') }}</td>
                                <td class="px-4 py-4">
                                    <span class="tag {{ $statusStyle }} text-button capitalize">{{ $proposal->status }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    @if($proposal->status === 'pending')
                                        <div class="flex justify-end">
                                            <details class="w-[320px]">
                                                <summary class="cursor-pointer text-primary font-semibold text-sm text-right">Edit / Exit</summary>
                                                <div class="mt-3 border border-line rounded-lg p-3 bg-surface space-y-3">
                                                    <form action="{{ route('proposals.update', $proposal) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div>
                                                            <label class="text-xs font-bold">Bid Amount</label>
                                                            <input type="number" name="bid_amount" min="1" value="{{ $proposal->bid_amount }}" class="w-full h-10 px-3 border border-line rounded" required>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs font-bold">Cover Letter</label>
                                                            <textarea name="cover_letter" rows="3" class="w-full px-3 py-2 border border-line rounded" required>{{ $proposal->cover_letter }}</textarea>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs font-bold">CV PDF (optional update)</label>
                                                            <input type="file" name="cv_file" accept=".pdf,application/pdf" class="w-full h-10 px-2 border border-line rounded">
                                                        </div>
                                                        @if($proposal->cv_file_url)
                                                            <label class="inline-flex items-center gap-2 text-xs">
                                                                <input type="checkbox" name="remove_cv" value="1">
                                                                Remove current CV
                                                            </label>
                                                        @endif
                                                        <button type="submit" class="button-main w-full h-10 text-sm">Update Proposal</button>
                                                    </form>

                                                    <form action="{{ route('proposals.destroy', $proposal) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full h-10 rounded border border-red-300 text-red-600 font-semibold hover:bg-red-50">Exit Job (Delete Proposal)</button>
                                                    </form>
                                                </div>
                                            </details>
                                        </div>
                                    @else
                                        <div class="text-right text-secondary text-sm">Locked</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-secondary">No proposals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal">
    <div class="modal_item menu_dashboard -modal overflow-hidden relative flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white" data-type="menu_dashboard">
        @include('partials.dashboard.candidate-menu', ['active' => 'proposals'])
    </div>
</div>
@endsection
