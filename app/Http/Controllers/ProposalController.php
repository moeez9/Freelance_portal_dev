<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Proposal;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    public function employerApplicants(Request $request)
    {
        $query = Proposal::with(['job', 'freelancer'])
            ->whereHas('job', fn ($q) => $q->where('employer_id', Auth::id()));

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('freelancer', fn ($sub) => $sub->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('job', fn ($sub) => $sub->where('title', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proposals = $query->latest()->paginate(12)->withQueryString();
        $selectedProposal = null;
        if ($request->filled('proposal')) {
            $selectedProposal = Proposal::with(['job', 'freelancer'])
                ->where('slug', $request->query('proposal'))
                ->whereHas('job', fn ($q) => $q->where('employer_id', Auth::id()))
                ->first();
        }

        return view('employers-applicants-jobs', compact('proposals', 'selectedProposal'));
    }

    public function myProposals()
    {
        $proposals = Proposal::with('job.employer')
            ->where('freelancer_id', Auth::id())
            ->latest()
            ->get();

        return view('candidates-proposals', compact('proposals'));
    }

    public function store(Request $request, Job $job)
    {
        $job->refreshStatusByDeadline();

        if (Auth::user()->role !== 'candidate') {
            return back()->with('error', 'Only freelancers can submit proposals.');
        }

        if ($job->employer_id === Auth::id()) {
            return back()->with('error', 'You cannot apply to your own job.');
        }

        if ($job->status !== 'open') {
            return back()->with('error', 'This job is not accepting new proposals.');
        }

        if ($job->isDeadlinePassed()) {
            return back()->with('error', 'Application deadline has passed for this job.');
        }

        if (Proposal::where('job_id', $job->id)->where('freelancer_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already submitted a proposal for this job.');
        }

        $request->validate([
            'cover_letter' => 'required|string',
            'bid_amount' => 'required|numeric|min:1',
            'cv_file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $proposalData = [
            'job_id' => $job->id,
            'freelancer_id' => Auth::id(),
            'cover_letter' => $request->cover_letter,
            'bid_amount' => $request->bid_amount,
        ];
        if ($request->hasFile('cv_file')) {
            $proposalData['cv_file_path'] = $request->file('cv_file')->store('proposals/cv', 'public');
        }

        Proposal::create($proposalData);

        UserNotification::create([
            'user_id' => $job->employer_id,
            'type' => 'job_bid',
            'title' => 'New bid received',
            'message' => Auth::user()->name . ' placed a bid on "' . $job->title . '".',
            'data' => [
                'job_id' => $job->id,
                'target_url' => route('employer.applicants'),
            ],
        ]);

        return back()->with('success', 'Proposal submitted successfully.');
    }

    public function update(Request $request, Proposal $proposal)
    {
        abort_if($proposal->freelancer_id !== Auth::id(), 403);
        if ($proposal->status !== 'pending') {
            return back()->with('error', 'Only pending proposals can be updated.');
        }

        $request->validate([
            'cover_letter' => 'required|string',
            'bid_amount' => 'required|numeric|min:1',
            'cv_file' => 'nullable|file|mimes:pdf|max:5120',
            'remove_cv' => 'nullable|in:0,1',
        ]);

        $data = [
            'cover_letter' => $request->cover_letter,
            'bid_amount' => $request->bid_amount,
        ];

        if ($request->input('remove_cv') === '1' && $proposal->cv_file_path) {
            Storage::disk('public')->delete($proposal->cv_file_path);
            $data['cv_file_path'] = null;
        }

        if ($request->hasFile('cv_file')) {
            if ($proposal->cv_file_path) {
                Storage::disk('public')->delete($proposal->cv_file_path);
            }
            $data['cv_file_path'] = $request->file('cv_file')->store('proposals/cv', 'public');
        }

        $proposal->update($data);

        return back()->with('success', 'Proposal updated successfully.');
    }

    public function destroy(Proposal $proposal)
    {
        abort_if($proposal->freelancer_id !== Auth::id(), 403);
        if ($proposal->status !== 'pending') {
            return back()->with('error', 'Only pending proposals can be removed.');
        }

        if ($proposal->cv_file_path) {
            Storage::disk('public')->delete($proposal->cv_file_path);
        }
        $proposal->delete();

        return back()->with('success', 'Proposal removed successfully.');
    }

    public function accept(Proposal $proposal)
    {
        $job = $proposal->job;
        if ($job->employer_id !== Auth::id()) {
            abort(403);
        }

        if ($job->accepted_proposal_id) {
            return back()->with('error', 'A proposal is already accepted for this job.');
        }

        DB::transaction(function () use ($proposal, $job) {
            $proposal->update(['status' => 'accepted']);
            $job->update(['accepted_proposal_id' => $proposal->id, 'status' => 'in_progress']);
            $job->proposals()->where('id', '!=', $proposal->id)->update(['status' => 'rejected']);
        });

        return back()->with('success', 'Proposal accepted.');
    }

    public function reject(Proposal $proposal)
    {
        $job = $proposal->job;
        if ($job->employer_id !== Auth::id()) {
            abort(403);
        }

        if ($proposal->status === 'accepted' || $job->accepted_proposal_id === $proposal->id) {
            return back()->with('error', 'Accepted proposal cannot be rejected.');
        }

        if ($proposal->status === 'rejected') {
            return back()->with('error', 'Proposal is already rejected.');
        }

        $proposal->update(['status' => 'rejected']);

        return back()->with('success', 'Proposal rejected.');
    }
}
