<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        Job::closeExpiredOpenJobs();

        $query = User::query()->withCount(['gigs', 'jobs'])->latest();

        if ($request->filled('role')) {
            $query->where('role', (string) $request->input('role'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        Job::closeExpiredOpenJobs();

        if ($user->role === 'candidate') {
            $items = $user->gigs()
                ->with('gigCategory')
                ->latest()
                ->paginate(10)
                ->withQueryString();
        } else {
            $items = $user->jobs()
                ->latest()
                ->paginate(10)
                ->withQueryString();
        }

        return view('admin.users.show', compact('user', 'items'));
    }

    public function showGig(User $user, Gig $gig)
    {
        abort_if($user->role !== 'candidate', 404);
        abort_if((int) $gig->freelancer_id !== (int) $user->id, 404);

        $gig->load(['gigCategory', 'gigSubcategory', 'gigServiceType', 'packages', 'requirements']);

        return view('admin.users.gig-show', compact('user', 'gig'));
    }

    public function showJob(User $user, Job $job)
    {
        abort_if($user->role !== 'employer', 404);
        abort_if((int) $job->employer_id !== (int) $user->id, 404);

        $job->load(['jobCategory', 'salaryType', 'employer']);

        return view('admin.users.job-show', compact('user', 'job'));
    }
}
