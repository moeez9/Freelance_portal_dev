@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container max-w-4xl">
        <div class="flex items-center justify-between gap-4 mb-5">
            <h4 class="heading4">Notifications</h4>
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="button-main -border">Mark all as read</button>
            </form>
        </div>

        <div class="bg-white rounded-lg border border-line overflow-hidden">
            <ul>
                @forelse($notifications as $notification)
                    <li class="border-b border-line last:border-b-0 {{ $notification->is_read ? 'bg-white' : 'bg-surface/70' }}">
                        <div class="p-5 hover:bg-surface duration-200">
                            <div class="flex items-start justify-between gap-4">
                                <a href="{{ route('notifications.read', $notification) }}" class="block flex-1">
                                    <p class="font-semibold text-title">{{ $notification->title }}</p>
                                    <p class="text-secondary mt-1">{{ $notification->message }}</p>
                                    <p class="text-xs text-secondary mt-2">{{ $notification->created_at?->diffForHumans() }}</p>
                                </a>
                                @if(!$notification->is_read)
                                    <span class="inline-flex w-2.5 h-2.5 rounded-full bg-primary mt-1 flex-shrink-0"></span>
                                @endif
                            </div>

                            @if(($notification->type ?? '') === 'gig_payment_verified' && !empty($notification->data['gig_order_id']))
                                <div class="flex items-center gap-3 mt-4">
                                    <a href="{{ route('notifications.read', $notification) }}" class="button-main -border !py-2 !px-4">
                                        View Details
                                    </a>
                                    <form method="POST" action="{{ route('conversations.store') }}">
                                        @csrf
                                        <input type="hidden" name="context_type" value="gig_order">
                                        <input type="hidden" name="context_id" value="{{ $notification->data['gig_order_id'] }}">
                                        <button type="submit" class="button-main !py-2 !px-4">Contact Buyer</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="p-8 text-center text-secondary">No data found</li>
                @endforelse
            </ul>
        </div>

        <div class="mt-4">
            {{ $notifications->onEachSide(1)->links() }}
        </div>
    </div>
</section>
@endsection
