<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\WorkSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkSubmissionController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $request->validate([
            'content' => 'required|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $proposal = $job->acceptedProposal;
        if (!$proposal || $proposal->freelancer_id !== Auth::id()) {
            abort(403, 'Only the accepted freelancer can submit work.');
        }

        $data = [
            'job_id' => $job->id,
            'freelancer_id' => Auth::id(),
            'content' => $request->content,
            'status' => 'submitted',
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('work-submissions', 'public');
        }

        WorkSubmission::create($data);

        return back()->with('success', 'Work submitted successfully.');
    }

    public function updateStatus(Request $request, WorkSubmission $submission)
    {
        if ($submission->job->employer_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,revision_requested',
        ]);

        DB::transaction(function () use ($submission, $request) {
            $submission->update(['status' => $request->status]);

            if ($request->status === 'approved') {
                $submission->job->update(['status' => 'completed']);
                return;
            }

            $submission->job->update(['status' => 'in_progress']);
        });

        return back()->with('success', 'Submission status updated.');
    }
}
