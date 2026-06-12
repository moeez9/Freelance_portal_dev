<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\SalaryType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function employerIndex()
    {
        Job::closeExpiredOpenJobs();

        $jobs = Job::where('employer_id', Auth::id())
            ->latest()
            ->get();

        return view('employers-jobs', compact('jobs'));
    }

    public function publicIndex()
    {
        Job::closeExpiredOpenJobs();

        $query = Job::with('employer')
            ->whereIn('status', ['open', 'closed']);

        if (request()->filled('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        if (request()->filled('category')) {
            $query->where('categories', request('category'));
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();

        return view('jobs-list', compact('jobs'));
    }

    public function create()
    {
        return view('employers-submit-jobs', [
            'isEdit' => false,
            'job' => null,
            'jobCategories' => JobCategory::orderBy('name')->get(),
            'salaryTypes' => SalaryType::orderBy('name')->get(),
        ]);
    }

    public function store(StoreJobRequest $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'employer') {
            abort(403);
        }

        $jobCategory = JobCategory::find($request->job_category_id);
        $salaryType = SalaryType::find($request->salary_type_id);

        $data = $request->only([
            'title',
            'deadline',
            'url',
            'email',
            'phone_no',
            'min',
            'max',
            'description',
            'requirements',
            'required_skills',
            'company_name',
            'job_location',
        ]);
        $data['job_category_id'] = $jobCategory?->id;
        $data['salary_type_id'] = $salaryType?->id;
        $data['categories'] = $jobCategory?->name ?? $request->categories;
        if (($jobCategory && $jobCategory->name === 'Other') || $request->categories === 'Other') {
            if ($request->filled('other_category')) {
                $data['categories'] = $request->other_category;
            }
        }
        $data['salary_type'] = $salaryType?->name ?? $request->salary_type;
        $data['employer_id'] = Auth::id();
        $data['status'] = 'open';
        $data['closed_by_employer'] = false;

        if ($request->hasFile('upload_logo')) {
            $data['upload_logo'] = $request->file('upload_logo')->store('jobs/logos', 'public');
        }

        if ($request->hasFile('upload_banner')) {
            $data['upload_banner'] = $request->file('upload_banner')->store('jobs/banners', 'public');
        }

        Job::create($data);

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', 'Job successfully posted');
    }

    public function employerShow(Job $job)
    {
        $job->refreshStatusByDeadline();
        $job->load('employer');
        abort_if($job->employer_id !== Auth::id(), 403);

        return response()->json([
            'id' => $job->id,
            'slug' => $job->slug,
            'title' => $job->title,
            'status' => $job->status,
            'closed_by_employer' => (bool) $job->closed_by_employer,
            'category' => $job->categories,
            'salary' => $job->min . ' - ' . $job->max,
            'deadline' => Carbon::parse($job->deadline)->format('F d, Y'),
            'description' => $job->description,
            'requirements' => $job->requirements,
            'required_skills' => $job->required_skills,
            'company_name' => $job->company_name,
            'job_location' => $job->job_location,
            'logo' => $job->upload_logo_url,
            'created_at' => Carbon::parse($job->created_at)->format('F d, Y'),
            'employer' => [
                'id' => $job->employer->id,
                'name' => $job->employer->name,
                'email' => $job->employer->email,
                'profile_pic' => $job->employer->profile_pic
                    ? asset('storage/' . $job->employer->profile_pic)
                    : null,
            ],
        ]);
    }

    public function publicShow(Job $job)
    {
        $job->refreshStatusByDeadline();

        $job->load([
            'employer',
            'acceptedProposal',
            'proposals' => fn ($q) => $q->with('freelancer')->latest(),
        ]);
        if (in_array($job->status, ['in_progress', 'completed'], true)) {
            $acceptedFreelancerId = $job->acceptedProposal?->freelancer_id;
            $allowed = Auth::check() && in_array(Auth::id(), [$job->employer_id, $acceptedFreelancerId], true);
            abort_if(!$allowed, 404);
        }

        return view('jobs-detail1', compact('job'));
    }

    public function edit(Job $job)
    {
        $job->refreshStatusByDeadline();
        abort_if($job->employer_id !== Auth::id(), 403);

        if ($job->status !== 'open') {
            return redirect()->route('employer.jobs.index')
                ->with('error', 'You cannot edit this job because it is already ' . $job->status);
        }

        return view('employers-submit-jobs', [
            'job' => $job,
            'isEdit' => true,
            'jobCategories' => JobCategory::orderBy('name')->get(),
            'salaryTypes' => SalaryType::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateJobRequest $request, Job $job)
    {
        abort_if($job->employer_id !== Auth::id(), 403);
        $job->refreshStatusByDeadline();

        if ($job->status !== 'open') {
            return redirect()->route('employer.jobs.index')
                ->with('error', 'Action denied. Job is not in open status.');
        }

        $jobCategory = JobCategory::find($request->job_category_id);
        $salaryType = SalaryType::find($request->salary_type_id);

        $data = $request->only([
            'title',
            'deadline',
            'url',
            'email',
            'phone_no',
            'min',
            'max',
            'description',
            'requirements',
            'required_skills',
            'company_name',
            'job_location',
        ]);
        $data['job_category_id'] = $jobCategory?->id;
        $data['salary_type_id'] = $salaryType?->id;
        $data['categories'] = $jobCategory?->name ?? $request->categories;
        if (($jobCategory && $jobCategory->name === 'Other') || $request->categories === 'Other') {
            if ($request->filled('other_category')) {
                $data['categories'] = $request->other_category;
            }
        }
        $data['salary_type'] = $salaryType?->name ?? $request->salary_type;

        if ($request->hasFile('upload_logo')) {
            $data['upload_logo'] = $request->file('upload_logo')->store('jobs/logos', 'public');
        }

        if ($request->hasFile('upload_banner')) {
            $data['upload_banner'] = $request->file('upload_banner')->store('jobs/banners', 'public');
        }

        $job->update($data);

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job updated successfully');
    }

    public function updateStatus(Request $request, Job $job)
    {
        abort_if($job->employer_id !== Auth::id(), 403);
        $job->refreshStatusByDeadline();

        $request->validate([
            'status' => 'required|in:open,closed',
        ]);

        if ($job->status === 'in_progress' || $job->status === 'completed') {
            return back()->with('error', 'You cannot manually change status for an active workflow job.');
        }

        if ($request->status === 'open') {
            if ($job->isDeadlinePassed()) {
                return back()->with('error', 'Deadline has passed. This job cannot be reopened.');
            }

            $job->update([
                'status' => 'open',
                'closed_by_employer' => false,
            ]);
        }

        if ($request->status === 'closed') {
            $job->update([
                'status' => 'closed',
                'closed_by_employer' => true,
            ]);
        }

        return back()->with('success', 'Job status updated successfully.');
    }

    public function destroy(Job $job)
    {
        $this->authorizeJob($job);

        $job->delete();

        return back()->with('success', 'Job deleted successfully');
    }
    private function authorizeJob(Job $job)
    {
        if ($job->employer_id !== Auth::id()) {
            abort(403);
        }
    }
}
