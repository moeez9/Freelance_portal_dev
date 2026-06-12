<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Gig;
use App\Models\GigOrder;
use App\Models\Job;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = Conversation::query()
            ->whereHas('participants', fn ($q) => $q->where('user_id', Auth::id()))
            ->with(['messages' => fn ($q) => $q->latest()->limit(1), 'participants.user'])
            ->latest()
            ->get();
        $this->appendConversationContextMeta($conversations);

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $conversation->load(['participants.user', 'messages.sender', 'messages.attachments']);
        $this->appendConversationContextMeta(collect([$conversation]));

        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->update(['last_read_at' => now()]);

        return view('messages.show', compact('conversation'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'context_type' => 'required|in:job,proposal,gig,gig_order',
            'context_id' => 'required|integer',
        ]);

        [$participantIds, $exists] = $this->resolveContextParticipants($data['context_type'], $data['context_id']);
        if (!$exists) {
            return back()->with('error', 'Invalid conversation context.');
        }

        if (!in_array(Auth::id(), $participantIds, true)) {
            abort(403);
        }

        $conversation = Conversation::firstOrCreate(
            [
                'context_type' => $data['context_type'],
                'context_id' => $data['context_id'],
            ],
            [
                'created_by' => Auth::id(),
            ]
        );

        foreach ($participantIds as $id) {
            ConversationParticipant::firstOrCreate([
                'conversation_id' => $conversation->id,
                'user_id' => $id,
            ]);
        }

        return redirect()->route('messages.show', $conversation);
    }

    private function authorizeConversation(Conversation $conversation): void
    {
        $isParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->exists();

        if (!$isParticipant) {
            abort(403);
        }
    }

    private function resolveContextParticipants(string $type, int $id): array
    {
        if ($type === 'job') {
            $job = Job::with('acceptedProposal')->find($id);
            if (!$job || !$job->acceptedProposal) {
                return [[], false];
            }
            return [[(int) $job->employer_id, (int) $job->acceptedProposal->freelancer_id], true];
        }

        if ($type === 'proposal') {
            $proposal = Proposal::with('job')->find($id);
            if (!$proposal || !$proposal->job) {
                return [[], false];
            }
            return [[(int) $proposal->job->employer_id, (int) $proposal->freelancer_id], true];
        }

        if ($type === 'gig') {
            $gig = Gig::find($id);
            if (!$gig) {
                return [[], false];
            }
            if (Auth::id() === (int) $gig->freelancer_id) {
                return [[], false];
            }
            return [[(int) $gig->freelancer_id, (int) Auth::id()], true];
        }

        $order = GigOrder::with('gig')->find($id);
        if (!$order) {
            return [[], false];
        }
        return [[(int) $order->client_id, (int) $order->gig->freelancer_id], true];
    }

    private function appendConversationContextMeta(Collection $conversations): void
    {
        if ($conversations->isEmpty()) {
            return;
        }

        $jobIds = $conversations->where('context_type', 'job')->pluck('context_id')->unique()->values();
        $proposalIds = $conversations->where('context_type', 'proposal')->pluck('context_id')->unique()->values();
        $gigIds = $conversations->where('context_type', 'gig')->pluck('context_id')->unique()->values();
        $gigOrderIds = $conversations->where('context_type', 'gig_order')->pluck('context_id')->unique()->values();

        $jobs = Job::query()->whereIn('id', $jobIds)->pluck('title', 'id');
        $proposals = Proposal::query()->with('job')->whereIn('id', $proposalIds)->get()->keyBy('id');
        $gigs = Gig::query()->whereIn('id', $gigIds)->pluck('title', 'id');
        $orders = GigOrder::query()->with(['gig', 'package'])->whereIn('id', $gigOrderIds)->get()->keyBy('id');

        foreach ($conversations as $conversation) {
            $conversation->context_label = 'General conversation';
            $conversation->context_summary = null;

            if ($conversation->context_type === 'job') {
                $title = $jobs[(int) $conversation->context_id] ?? 'Job';
                $conversation->context_label = 'Job chat';
                $conversation->context_summary = $title;
                continue;
            }

            if ($conversation->context_type === 'proposal') {
                $proposal = $proposals[(int) $conversation->context_id] ?? null;
                $title = $proposal?->job?->title ?? 'Job proposal';
                $conversation->context_label = 'Proposal chat';
                $conversation->context_summary = $title;
                continue;
            }

            if ($conversation->context_type === 'gig') {
                $title = $gigs[(int) $conversation->context_id] ?? 'Gig service';
                $conversation->context_label = 'Gig inquiry';
                $conversation->context_summary = $title;
                continue;
            }

            if ($conversation->context_type === 'gig_order') {
                $order = $orders[(int) $conversation->context_id] ?? null;
                $title = $order?->gig?->title ?? 'Gig order';
                $package = $order?->package?->name ? ' (' . $order->package->name . ')' : '';
                $conversation->context_label = 'Gig order chat';
                $conversation->context_summary = $title . $package;
            }
        }
    }
}
