@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container">
        <div class="flex items-center justify-between mb-6">
            <h3 class="heading3">Inbox</h3>
        </div>

        <div class="bg-white rounded-lg border border-line shadow-sm overflow-hidden">
            @forelse($conversations as $conversation)
                @php
                    $lastMessage = $conversation->messages->first();
                    $other = $conversation->participants->firstWhere('user_id', '!=', auth()->id());
                @endphp
                <a href="{{ route('messages.show', $conversation) }}" class="flex items-center justify-between gap-3 px-5 py-4 border-b border-line hover:bg-gray-50">
                    <div>
                        <div class="font-bold text-title">{{ $other?->user?->name ?? 'Conversation' }}</div>
                        <div class="text-xs text-primary mt-0.5">
                            {{ $conversation->context_label ?? 'Conversation' }}
                            @if(!empty($conversation->context_summary))
                                : {{ $conversation->context_summary }}
                            @endif
                        </div>
                        <div class="text-sm text-secondary">{{ $lastMessage?->body ?? 'No messages yet' }}</div>
                    </div>
                    <div class="text-xs text-secondary">{{ optional($lastMessage?->created_at)->diffForHumans() }}</div>
                </a>
            @empty
                <div class="px-6 py-10 text-center text-secondary">No conversations found.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection
