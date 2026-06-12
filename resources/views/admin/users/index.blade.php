@extends('layouts.app')

@section('content')
<section class="pt-24 sm:pt-28 pb-12 sm:pb-16 bg-surface min-h-screen">
    <div class="container max-w-6xl">
        <div class="bg-white rounded-lg border border-line p-4 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h4 class="heading4">Users & Registration Roles</h4>
                    <p class="text-sm text-secondary mt-1">Candidate (freelancer) aur Employee/Client (employer) registrations.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="button-main -border w-full sm:w-auto text-center">Back to Dashboard</a>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name or email"
                    class="w-full rounded-md border-gray-300"
                >
                <select name="role" class="w-full rounded-md border-gray-300">
                    <option value="">All roles</option>
                    <option value="candidate" @selected(request('role') === 'candidate')>Candidate</option>
                    <option value="employer" @selected(request('role') === 'employer')>Employee/Client</option>
                </select>
                <button type="submit" class="button-main w-full">Apply Filters</button>
            </form>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($users as $user)
                    <a href="{{ route('admin.users.show', ['user' => $user->slug ?: $user->id]) }}" class="block border border-line rounded-lg p-4 hover:bg-surface duration-200">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-title truncate">{{ $user->name }}</p>
                                <p class="text-sm text-secondary truncate">{{ $user->email }}</p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $user->role === 'candidate' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $user->role === 'candidate' ? 'Candidate' : 'Employee/Client' }}
                            </span>
                        </div>
                        <div class="mt-3 text-sm text-secondary">
                            @if($user->role === 'candidate')
                                Gigs posted: <strong class="text-title">{{ $user->gigs_count }}</strong>
                            @else
                                Jobs posted: <strong class="text-title">{{ $user->jobs_count }}</strong>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="sm:col-span-2 lg:col-span-3 text-center py-8 text-secondary">No users found.</div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
