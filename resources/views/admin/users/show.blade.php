@extends('layouts.app')

@section('content')
<section class="pt-24 sm:pt-28 pb-12 sm:pb-16 bg-surface min-h-screen">
    <div class="container max-w-6xl">
        <div class="bg-white rounded-lg border border-line p-4 sm:p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h4 class="heading4 break-words">{{ $user->name }}</h4>
                    <p class="text-sm text-secondary break-all">{{ $user->email }}</p>
                    <p class="mt-2">
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $user->role === 'candidate' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                            Registered as {{ $user->role === 'candidate' ? 'Candidate (Freelancer)' : 'Employee/Client (Employer)' }}
                        </span>
                    </p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="button-main -border w-full md:w-auto text-center">Back to Users</a>
            </div>

            <div class="mt-6">
                <h5 class="heading5 mb-3">
                    {{ $user->role === 'candidate' ? 'Posted Gigs' : 'Posted Jobs' }}
                </h5>

                <div class="overflow-x-auto border border-line rounded-lg">
                    <table class="w-full min-w-[700px]">
                        <thead class="bg-surface border-b border-line">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs uppercase text-secondary">Title</th>
                                <th class="text-left px-4 py-3 text-xs uppercase text-secondary">Category</th>
                                <th class="text-left px-4 py-3 text-xs uppercase text-secondary">Status</th>
                                <th class="text-left px-4 py-3 text-xs uppercase text-secondary">Created</th>
                                <th class="text-right px-4 py-3 text-xs uppercase text-secondary">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr class="border-b border-line/70">
                                    <td class="px-4 py-3 text-sm">{{ $item->title }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $user->role === 'candidate' ? ($item->category ?? 'N/A') : ($item->categories ?? 'N/A') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm capitalize">{{ $item->status }}</td>
                                    <td class="px-4 py-3 text-sm whitespace-nowrap">{{ $item->created_at?->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if($user->role === 'candidate')
                                            <a href="{{ route('admin.users.gigs.show', ['user' => $user, 'gig' => $item]) }}" class="text-primary underline text-sm">View</a>
                                        @else
                                            <a href="{{ route('admin.users.jobs.show', ['user' => $user, 'job' => $item]) }}" class="text-primary underline text-sm">View</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-secondary">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
