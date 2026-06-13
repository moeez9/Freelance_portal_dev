@extends('layouts.app')

@section('content')
<div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex">
    <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
        @include('partials.dashboard.candidate-menu', ['active' => 'orders'])
    </div>

    <div class="content_dashboard scrollbar_custom max-h-full w-full h-fit bg-surface">
        <div class="container h-full lg:py-15 sm:py-12 py-8">
            <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden" data-type="menu_dashboard">
                <span class="ph ph-squares-four text-xl"></span>
                <strong class="text-button">Menu</strong>
            </button>

            <div class="flex items-center justify-between gap-4 mb-6 mt-3">
                <h4 class="heading4">Gig Orders</h4>
                <span class="text-secondary text-sm">Only admin-verified payments are shown</span>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gig</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verified At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $order->client?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <a href="{{ route('services.show', ['gig' => $order->gig, 'package' => $order->package?->type]) }}" class="text-blue-600 hover:underline">
                                            {{ $order->gig?->title ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $order->package?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${{ number_format((float) ($order->package?->price ?? 0), 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $order->payment_verified_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No verified orders found yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(method_exists($orders, 'links'))
                <div class="mt-4">
                    {{ $orders->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal">
    <div class="modal_item menu_dashboard -modal overflow-hidden relative flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white" data-type="menu_dashboard">
        @include('partials.dashboard.candidate-menu', ['active' => 'orders'])
    </div>
</div>
@endsection
