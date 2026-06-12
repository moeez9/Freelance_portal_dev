@php
    $isMine = (int) $message->sender_id === (int) auth()->id();
@endphp
<div class="{{ $isMine ? 'text-right' : 'text-left' }}" data-message-id="{{ $message->id }}">
    @if($message->body)
        <div class="inline-block px-4 py-2 rounded-lg {{ $isMine ? 'bg-primary text-white' : 'bg-gray-100 text-gray-800' }}">
            {{ $message->body }}
        </div>
    @endif
    @if($message->attachments->count())
        <div class="mt-1 space-y-1">
            @foreach($message->attachments as $attachment)
                <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="text-xs text-blue-600 underline block">
                    {{ $attachment->original_name }}
                </a>
            @endforeach
        </div>
    @endif
    <div class="text-xs text-secondary mt-1">{{ $message->sender->name }} - {{ $message->created_at->diffForHumans() }}</div>
</div>
