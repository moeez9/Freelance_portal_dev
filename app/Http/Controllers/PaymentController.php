<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function release(Request $request, Job $job)
    {
        if ($job->employer_id !== Auth::id()) {
            abort(403);
        }

        $latestSubmission = $job->workSubmissions()->latest()->first();
        if (!$latestSubmission || $latestSubmission->status !== 'approved') {
            return back()->with('error', 'Payment can be released only after approved work.');
        }

        DB::transaction(function () use ($job) {
            Payment::updateOrCreate(
                [
                    'type' => 'job',
                    'reference_id' => $job->id,
                ],
                [
                    'job_id' => $job->id,
                    'user_id' => Auth::id(),
                    'amount' => $job->max,
                    'status' => 'released',
                ]
            );

            $job->update(['status' => 'closed']);
        });

        return back()->with('success', 'Payment released successfully.');
    }
}
