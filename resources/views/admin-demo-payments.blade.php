@extends('layouts.app')

@section('content')
<div class="container pt-[120px] pb-20">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Admin Demo Payments</h1>
            @if(!empty($adminEmail))
                <p class="text-sm text-gray-500 mt-1">{{ $adminEmail }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded border bg-white text-gray-700 border-gray-300">Dashboard</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded border bg-white text-gray-700 border-gray-300">Logout</button>
            </form>
        </div>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.demo.payments', ['status' => 'pending']) }}" class="px-4 py-2 rounded border {{ request('status') === 'pending' ? 'bg-[#04b2b2] text-white border-[#04b2b2]' : 'bg-white text-gray-700 border-gray-300' }}">
                Pending
            </a>
            <a href="{{ route('admin.demo.payments', ['status' => 'verified']) }}" class="px-4 py-2 rounded border {{ request('status') === 'verified' ? 'bg-[#04b2b2] text-white border-[#04b2b2]' : 'bg-white text-gray-700 border-gray-300' }}">
                Verified
            </a>
            <a href="{{ route('admin.demo.payments') }}" class="px-4 py-2 rounded border {{ !request('status') ? 'bg-[#04b2b2] text-white border-[#04b2b2]' : 'bg-white text-gray-700 border-gray-300' }}">
                All
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Gig</th>
                    <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Package</th>
                    <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Employer</th>
                    <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Freelancer</th>
                    <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Method</th>
                    <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Reference</th>
                    <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs uppercase text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3">{{ $order->gig?->title ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold">{{ $order->package?->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">${{ number_format((float) ($order->package?->price ?? 0), 2) }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $order->client?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $order->gig?->freelancer?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', (string) $order->payment_method) ?: 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $order->transaction_reference ?: 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @if($order->payment_verified_at)
                                <span class="inline-flex px-2 py-1 rounded text-xs bg-green-100 text-green-700">Verified</span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if(!$order->payment_verified_at)
                                <form action="{{ route('admin.demo.payments.verify', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded bg-[#04b2b2] text-white text-sm">
                                        Verify
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-500">No action</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-500">No data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->onEachSide(1)->links() }}
    </div>
</div>
@endsection
