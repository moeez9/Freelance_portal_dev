<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\Job;
use App\Models\Proposal;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function candidate()
    {
        $userId = Auth::id();

        $stats = [
            'applied_jobs' => Proposal::where('freelancer_id', $userId)->count(),
            'active_proposals' => Proposal::where('freelancer_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'accepted_proposals' => Proposal::where('freelancer_id', $userId)
                ->where('status', 'accepted')
                ->count(),
            'total_services' => Gig::where('freelancer_id', $userId)->count(),
            'total_reviews' => Review::where('reviewee_id', $userId)->count(),
        ];

        $recentProposals = Proposal::with(['job.employer'])
            ->where('freelancer_id', $userId)
            ->latest()
            ->take(8)
            ->get();

        $recentServices = Gig::where('freelancer_id', $userId)
            ->latest()
            ->take(8)
            ->get();

        $months = collect(range(5, 0))->map(fn ($offset) => Carbon::now()->subMonths($offset));
        $months->push(Carbon::now());

        $chartLabels = $months->map(fn ($month) => $month->format('M Y'))->values();
        $proposalSeries = $months->map(function ($month) use ($userId) {
            return Proposal::where('freelancer_id', $userId)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        })->values();
        $serviceSeries = $months->map(function ($month) use ($userId) {
            return Gig::where('freelancer_id', $userId)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        })->values();

        return view('candidates-dashboard', compact(
            'stats',
            'recentProposals',
            'recentServices',
            'chartLabels',
            'proposalSeries',
            'serviceSeries'
        ));
    }

    public function employer()
    {
        $userId = Auth::id();

        $stats = [
            'posted_jobs' => Job::where('employer_id', $userId)->count(),
            'open_jobs' => Job::where('employer_id', $userId)->where('status', 'open')->count(),
            'closed_jobs' => Job::where('employer_id', $userId)->where('status', 'closed')->count(),
            'total_applications' => Proposal::whereHas('job', fn ($q) => $q->where('employer_id', $userId))->count(),
            'total_reviews' => Review::where('reviewee_id', $userId)->count(),
        ];

        $recentJobs = Job::where('employer_id', $userId)
            ->latest()
            ->take(8)
            ->get();

        $recentApplicants = Proposal::with(['job', 'freelancer'])
            ->whereHas('job', fn ($q) => $q->where('employer_id', $userId))
            ->latest()
            ->take(8)
            ->get();

        $months = collect(range(5, 0))->map(fn ($offset) => Carbon::now()->subMonths($offset));
        $months->push(Carbon::now());

        $chartLabels = $months->map(fn ($month) => $month->format('M Y'))->values();
        $jobsSeries = $months->map(function ($month) use ($userId) {
            return Job::where('employer_id', $userId)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        })->values();
        $applicantsSeries = $months->map(function ($month) use ($userId) {
            return Proposal::whereHas('job', fn ($query) => $query->where('employer_id', $userId))
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        })->values();

        return view('employers-dashboard', compact(
            'stats',
            'recentJobs',
            'recentApplicants',
            'chartLabels',
            'jobsSeries',
            'applicantsSeries'
        ));
    }
}
