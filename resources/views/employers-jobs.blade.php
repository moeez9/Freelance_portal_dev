@extends('layouts.app')

@section('content')
<div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex">
    <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
        @include('partials.dashboard.employer-menu', ['active' => 'jobs'])
    </div>

    <div class="content_dashboard scrollbar_custom max-h-full w-full h-fit bg-surface">
        <div class="container h-full lg:py-15 sm:py-12 py-8">
            <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden" data-type="menu_dashboard">
                <span class="ph ph-squares-four text-xl"></span>
                <strong class="text-button">Menu</strong>
            </button>

            <div class="flex justify-between items-center mb-6 mt-3">
                <h1 class="text-2xl font-bold text-gray-900">My Posted Jobs</h1>
                <a href="{{ route('employer.jobs.create') }}" class="bg-[#04b2b2] text-white px-6 py-2 rounded font-bold text-sm hover:bg-[#04d3d3] transition">
                    POST A NEW JOB
                </a>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($jobs as $job)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-blue-600 hover:underline">
                                        <button type="button" class="btn_view_job" data-job-id="{{ $job->slug }}">{{ $job->title }}</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $job->categories }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $job->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                        @if($job->status === 'closed' && $job->closed_by_employer)
                                            <span class="text-[11px] text-gray-500">Closed by Employer</span>
                                        @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('employer.jobs.edit', $job) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @if(\Carbon\Carbon::parse($job->deadline)->gte(\Carbon\Carbon::today()) && !in_array($job->status, ['in_progress', 'completed'], true))
                                        @if($job->status === 'open')
                                            <form action="{{ route('employer.jobs.updateStatus', $job) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="closed">
                                                <button type="submit" class="text-amber-600 hover:text-amber-800">Close</button>
                                            </form>
                                        @elseif($job->status === 'closed')
                                            <form action="{{ route('employer.jobs.updateStatus', $job) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="open">
                                                <button type="submit" class="text-green-600 hover:text-green-800">Reopen</button>
                                            </form>
                                        @endif
                                    @endif
                                    <form action="{{ route('employer.jobs.destroy', $job) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No jobs posted yet.</td>
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
        @include('partials.dashboard.employer-menu', ['active' => 'jobs'])
    </div>
</div>
@endsection
