@extends('layouts.app')

@section('content')
<div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex sm:pt-20 pt-16">
    <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
        @include('partials.dashboard.candidate-menu', ['active' => 'services'])
    </div>

    <div class="content_dashboard scrollbar_custom max-h-full w-full h-fit bg-surface">
        <div class="container h-full lg:py-15 sm:py-12 py-8">
            <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden" data-type="menu_dashboard">
                <span class="ph ph-squares-four text-xl"></span>
                <strong class="text-button">Menu</strong>
            </button>

            <div class="flex justify-between items-center mb-6 mt-3">
                <h1 class="text-2xl font-bold text-gray-900">My Gigs</h1>
                <a href="{{ route('gigs.create') }}" class="bg-[#04b2b2] text-white px-6 py-2 rounded font-bold text-sm hover:bg-[#04d3d3] transition">
                    CREATE A NEW GIG
                </a>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gig</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($gigs as $gig)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-16 flex-shrink-0 bg-gray-200 rounded overflow-hidden">
                                            <img src="{{ $gig->thumbnail ? asset('storage/' . $gig->thumbnail) : 'https://via.placeholder.com/150' }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-blue-600 hover:underline">
                                                <a href="{{ route('services.show', $gig->slug) }}">{{ $gig->title }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $gig->category }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $gig->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($gig->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-4">
                                    <a href="{{ route('gigs.edit', $gig->slug) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">Edit</a>

                                    <form action="{{ route('gigs.status', $gig->slug) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-xs border-gray-300 rounded">
                                            <option value="active" {{ $gig->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="paused" {{ $gig->status === 'paused' ? 'selected' : '' }}>Pause</option>
                                            <option value="deleted" {{ $gig->status === 'deleted' ? 'selected' : '' }}>Delete</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No gigs found.</td>
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
        @include('partials.dashboard.candidate-menu', ['active' => 'services'])
    </div>
</div>
@endsection
