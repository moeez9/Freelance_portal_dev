@extends('layouts.app')

@section('content')
<section class="pt-32 pb-16">
    <div class="container">
        @php
            $other = $conversation->participants->firstWhere('user_id', '!=', auth()->id());
        @endphp
        <div class="bg-white rounded-lg border border-line shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-line flex items-center justify-between">
                <div>
                    <h5 class="heading5">{{ $other?->user?->name ?? 'Conversation' }}</h5>
                    <div class="text-xs text-primary mt-1">
                        {{ $conversation->context_label ?? 'Conversation' }}
                        @if(!empty($conversation->context_summary))
                            : {{ $conversation->context_summary }}
                        @endif
                    </div>
                </div>
                <a href="{{ route('messages.index') }}" class="text-primary">Back to Inbox</a>
            </div>

            <div id="message-list" class="p-5 space-y-4 max-h-[500px] overflow-y-auto">
                @foreach($conversation->messages as $message)
                    @include('messages.partials.message-item', ['message' => $message])
                @endforeach
            </div>

            <div id="message-error" class="hidden px-5 pt-4 text-sm text-red-600"></div>

            <form id="message-form" action="{{ route('messages.store', $conversation) }}" method="POST" enctype="multipart/form-data" class="p-5 border-t border-line space-y-3">
                @csrf
                <textarea id="message-body" name="body" rows="3" class="w-full border-gray-300 rounded-md" placeholder="Type your message..."></textarea>
                <input id="message-attachments" type="file" name="attachments[]" multiple class="w-full border-gray-300 rounded-md">
                <button id="message-submit" type="submit" class="button-main">Send Message</button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const conversationId = {{ $conversation->id }};
    const authId = {{ auth()->id() }};
    const list = document.getElementById('message-list');
    const form = document.getElementById('message-form');
    const submitButton = document.getElementById('message-submit');
    const errorBox = document.getElementById('message-error');
    const bodyInput = document.getElementById('message-body');
    const attachmentsInput = document.getElementById('message-attachments');
    const latestUrl = @json(route('messages.latest', $conversation));
    let lastMessageId = 0;

    const escapeHtml = (unsafe) => {
        return String(unsafe ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const renderMessage = (message) => {
        const mine = Number(message.sender_id) === Number(authId);
        const wrapper = document.createElement('div');
        wrapper.className = mine ? 'text-right' : 'text-left';
        wrapper.dataset.messageId = message.id;

        let html = '';
        if (message.body) {
            html += `<div class="inline-block px-4 py-2 rounded-lg ${mine ? 'bg-primary text-white' : 'bg-gray-100 text-gray-800'}">${escapeHtml(message.body)}</div>`;
        }
        if (Array.isArray(message.attachments) && message.attachments.length > 0) {
            const attachmentsHtml = message.attachments.map((a) =>
                `<a href="${escapeHtml(a.url)}" target="_blank" class="text-xs text-blue-600 underline block">${escapeHtml(a.original_name)}</a>`
            ).join('');
            html += `<div class="mt-1 space-y-1">${attachmentsHtml}</div>`;
        }
        html += `<div class="text-xs text-secondary mt-1">${escapeHtml(message.sender_name || 'User')} - ${escapeHtml(message.created_human || 'just now')}</div>`;
        wrapper.innerHTML = html;
        return wrapper;
    };

    const appendMessage = (message) => {
        if (!list) return;
        if (list.querySelector(`[data-message-id="${message.id}"]`)) return;
        list.appendChild(renderMessage(message));
        list.scrollTop = list.scrollHeight;
        if (Number(message.id) > Number(lastMessageId)) {
            lastMessageId = Number(message.id);
        }
    };

    if (list) {
        list.scrollTop = list.scrollHeight;
        const ids = Array.from(list.querySelectorAll('[data-message-id]')).map((el) => Number(el.dataset.messageId || 0));
        lastMessageId = ids.length ? Math.max(...ids) : 0;
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Sending...';
            }
            if (errorBox) {
                errorBox.classList.add('hidden');
                errorBox.textContent = '';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new FormData(form),
                });
                const result = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(result.message || 'Message not sent.');
                }
                if (result && result.data) {
                    appendMessage(result.data);
                }
                form.reset();
                if (bodyInput) bodyInput.focus();
            } catch (error) {
                if (errorBox) {
                    errorBox.textContent = error.message || 'Message not sent.';
                    errorBox.classList.remove('hidden');
                }
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Send Message';
                }
                if (attachmentsInput) attachmentsInput.value = '';
            }
        });
    }

    if (window.Echo) {
        window.Echo.private('conversation.' + conversationId)
            .listen('.message.sent', function (event) {
                if (!event || !event.message) return;
                appendMessage({
                    id: event.message.id,
                    body: event.message.body,
                    sender_id: event.message.sender_id,
                    sender_name: event.message.sender?.name,
                    created_human: 'just now',
                    attachments: (event.message.attachments || []).map((a) => ({
                        url: '/storage/' + a.path,
                        original_name: a.original_name,
                    })),
                });
            });
    }

    let polling = false;
    let pollTimer = null;
    const activeInterval = 2000;
    const backgroundInterval = 8000;
    const pollLatest = async () => {
        if (polling) return;
        polling = true;
        try {
            const response = await fetch(`${latestUrl}?after_id=${lastMessageId}`, {
                headers: { 'Accept': 'application/json' },
            });
            if (!response.ok) return;
            const result = await response.json().catch(() => ({}));
            const items = Array.isArray(result.data) ? result.data : [];
            items.forEach(appendMessage);
        } catch (e) {
            // Silent fail; next interval will retry.
        } finally {
            polling = false;
        }
    };

    const restartPolling = () => {
        if (pollTimer) {
            clearInterval(pollTimer);
        }
        const interval = document.hidden ? backgroundInterval : activeInterval;
        pollTimer = setInterval(pollLatest, interval);
    };

    document.addEventListener('visibilitychange', restartPolling);
    restartPolling();
});
</script>
@endpush
