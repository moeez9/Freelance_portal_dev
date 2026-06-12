<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function latest(Request $request, Conversation $conversation)
    {
        $isParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isParticipant) {
            abort(403);
        }

        $afterId = (int) $request->integer('after_id', 0);

        $messages = Message::query()
            ->where('conversation_id', $conversation->id)
            ->when($afterId > 0, fn ($q) => $q->where('id', '>', $afterId))
            ->with(['sender', 'attachments'])
            ->orderBy('id')
            ->get();

        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->update(['last_read_at' => now()]);

        return response()->json([
            'data' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender?->name,
                    'created_human' => $message->created_at?->diffForHumans(),
                    'created_iso' => $message->created_at?->toIso8601String(),
                    'attachments' => $message->attachments->map(function ($attachment) {
                        return [
                            'url' => asset('storage/' . $attachment->path),
                            'original_name' => $attachment->original_name,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $isParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isParticipant) {
            abort(403);
        }

        $request->validate([
            'body' => 'nullable|string|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240',
        ]);

        if (!$request->filled('body') && !$request->hasFile('attachments')) {
            $errorMessage = 'Message not sent. Please add text or attachment.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 422);
            }
            return back()->with('error', $errorMessage);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $request->body,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $message->attachments()->create([
                    'path' => $file->store('message-attachments', 'public'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->update(['last_read_at' => now()]);

        $message->load(['sender', 'attachments']);

        broadcast(new MessageSent($message))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'ok',
                'data' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender?->name,
                    'created_human' => $message->created_at?->diffForHumans(),
                    'created_iso' => $message->created_at?->toIso8601String(),
                    'attachments' => $message->attachments->map(function ($attachment) {
                        return [
                            'url' => asset('storage/' . $attachment->path),
                            'original_name' => $attachment->original_name,
                        ];
                    })->values(),
                ],
            ]);
        }

        return back();
    }
}
