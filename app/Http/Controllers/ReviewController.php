<?php

namespace App\Http\Controllers;

use App\Models\GigOrder;
use App\Models\Job;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'job_id' => 'nullable|exists:jobs,id',
            'gig_order_id' => 'nullable|exists:gig_orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        if (!$data['job_id'] && !$data['gig_order_id']) {
            return back()->with('error', 'Review context is required.');
        }

        if ($data['job_id']) {
            return $this->storeJobReview($data);
        }

        return $this->storeGigReview($data);
    }

    private function storeJobReview(array $data)
    {
        $job = Job::with(['acceptedProposal', 'workSubmissions'])->findOrFail($data['job_id']);
        $submission = $job->workSubmissions()->latest()->first();
        if (!$submission || $submission->status !== 'approved') {
            return back()->with('error', 'Job review is allowed only after completion.');
        }

        if (!in_array(Auth::id(), [$job->employer_id, $job->acceptedProposal?->freelancer_id], true)) {
            abort(403);
        }

        $revieweeId = Auth::id() === $job->employer_id
            ? $job->acceptedProposal?->freelancer_id
            : $job->employer_id;

        if (!$revieweeId) {
            return back()->with('error', 'Job review target is invalid.');
        }

        $exists = Review::where('job_id', $job->id)->where('reviewer_id', Auth::id())->exists();
        if ($exists) {
            return back()->with('error', 'You have already reviewed this completed job.');
        }

        Review::create([
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $revieweeId,
            'job_id' => $job->id,
            'proposal_id' => $job->accepted_proposal_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return back()->with('success', 'Review submitted.');
    }

    private function storeGigReview(array $data)
    {
        $order = GigOrder::with('gig')->findOrFail($data['gig_order_id']);
        if ($order->status !== 'completed') {
            return back()->with('error', 'Gig review is allowed only after completion.');
        }

        if (!in_array(Auth::id(), [$order->client_id, $order->gig->freelancer_id], true)) {
            abort(403);
        }

        $revieweeId = Auth::id() === $order->client_id
            ? $order->gig->freelancer_id
            : $order->client_id;

        $exists = Review::where('gig_id', $order->gig_id)->where('reviewer_id', Auth::id())->exists();
        if ($exists) {
            return back()->with('error', 'You have already reviewed this completed gig.');
        }

        Review::create([
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $revieweeId,
            'gig_id' => $order->gig_id,
            'gig_order_id' => $order->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return back()->with('success', 'Review submitted.');
    }
}
