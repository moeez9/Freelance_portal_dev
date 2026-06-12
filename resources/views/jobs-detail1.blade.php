@extends('layouts.app')

@section('content')
<section class="breadcrumb bg-[#F9F2EC] sm:pt-35 pt-30 pb-15">
    <div class="container flex max-lg:flex-col lg:items-center justify-between gap-7 gap-y-4">
        <div class="jobs_info flex flex-wrap sm:gap-8 gap-4 w-full">
            <div class="overflow-hidden flex-shrink-0 sm:w-[100px] w-24 sm:h-[100px] h-24 rounded-full">
                <img src="{{ $job->upload_logo_url ?? 'https://freelanhub.vercel.app/assets/images/company/8.png' }}" alt="logo" class="jobs_avatar w-full h-full object-cover" />
            </div>
            <div class="flex flex-col gap-1">
                <span class="jobs_company text-button text-primary">{{ $job->employer->name }}</span>
                <h4 class="jobs_name heading4 -style-1">{{ $job->title }}</h4>
                <div class="flex flex-wrap items-center gap-5 gap-y-1.5 mt-1">
                    <div class="jobs_address -style-1 text-secondary">
                        <i class="fa-solid fa-layer-group text-xl"></i>
                        <span class="address align-top">{{ $job->categories }}</span>
                    </div>
                    <div class="jobs_date text-secondary">
                        <span class="ph ph-calendar-blank text-xl"></span>
                        <span class="date align-top">{{ $job->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="breadcrumb_action flex flex-col lg:text-right">
            <div class="jobs_price">
                <span class="price text-title">${{ $job->min }} - ${{ $job->max }}</span>
                <span class="text-secondary">/ {{ $job->salary_type }}</span>
            </div>
            <div class="mt-2">
                @if($job->status === 'open')
                    <span class="inline-flex px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-sm font-semibold">Open</span>
                @else
                    <span class="inline-flex px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-semibold">Closed</span>
                    @if($job->closed_by_employer)
                        <p class="text-xs text-secondary mt-1">Closed by Employer</p>
                    @endif
                @endif
            </div>
            <div class="mt-2">
                <span class="body2 text-red-600 font-bold">Deadline: {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}</span>
            </div>
        </div>
    </div>
</section>

<section class="jobs_detail lg:py-20 sm:py-14 py-10">
    <div class="container flex max-lg:flex-col gap-y-10">
        <div class="jobs_info w-full lg:w-2/3 lg:pr-15">
            <div class="description">
                <h6 class="heading6">Job Description</h6>
                <p class="mt-3 body2 text-secondary whitespace-pre-line">{{ $job->description }}</p>
            </div>

            <div class="mt-8">
                <h6 class="heading6">Requirements</h6>
                <p class="mt-3 body2 text-secondary whitespace-pre-line">{{ $job->requirements ?? 'N/A' }}</p>
            </div>

            <div class="mt-8">
                <h6 class="heading6">Required Skills</h6>
                <p class="mt-3 body2 text-secondary whitespace-pre-line">{{ $job->required_skills ?? 'N/A' }}</p>
            </div>

            @auth
                @if(Auth::user()->role === 'candidate' && $job->status === 'open' && \Carbon\Carbon::parse($job->deadline)->gte(\Carbon\Carbon::today()))
                    <div class="proposal_form mt-10 p-6 bg-white rounded-lg shadow-md border border-line">
                        <h6 class="heading6 mb-4">Submit Your Proposal</h6>
                        <form action="{{ route('proposals.store', $job) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Bid Amount ($)</label>
                                    <input type="number" name="bid_amount" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Cover Letter</label>
                                    <textarea name="cover_letter" rows="5" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Why should we hire you?"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">CV (PDF Only)</label>
                                    <input type="file" name="cv_file" accept=".pdf,application/pdf" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <button type="submit" class="button-main w-full">Submit Proposal</button>
                            </div>
                        </form>
                    </div>
                @endif

                @if(Auth::user()->role === 'candidate' && ($job->status !== 'open' || \Carbon\Carbon::parse($job->deadline)->lt(\Carbon\Carbon::today())))
                    <div class="mt-10 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                        This job is closed and no longer accepting applications.
                    </div>
                @endif

                @if(Auth::user()->role === 'candidate' && $job->acceptedProposal && $job->acceptedProposal->freelancer_id === Auth::id())
                    <div class="proposal_form mt-10 p-6 bg-white rounded-lg shadow-md border border-line">
                        <h6 class="heading6 mb-4">Submit Work</h6>
                        <form action="{{ route('submissions.store', $job) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <textarea name="content" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Share completed work details"></textarea>
                            <input type="file" name="file" class="w-full border-gray-300 rounded-md shadow-sm">
                            <button type="submit" class="button-main w-full">Submit Work</button>
                        </form>
                    </div>
                @endif

                @if(Auth::user()->id === $job->employer_id)
                    <div class="mt-10 p-6 bg-white rounded-lg shadow-md border border-line">
                        <h6 class="heading6 mb-4">Employer Actions</h6>
                        @if(\Carbon\Carbon::parse($job->deadline)->gte(\Carbon\Carbon::today()) && !in_array($job->status, ['in_progress', 'completed'], true))
                            <div class="mb-4">
                                @if($job->status === 'open')
                                    <form action="{{ route('employer.jobs.updateStatus', $job) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="closed">
                                        <button type="submit" class="button-main w-full bg-gray-700 hover:bg-gray-800">Close Job (by Employer)</button>
                                    </form>
                                @elseif($job->status === 'closed')
                                    <form action="{{ route('employer.jobs.updateStatus', $job) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="open">
                                        <button type="submit" class="button-main w-full">Reopen Job</button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        @php $latestSubmission = $job->workSubmissions()->latest()->first(); @endphp
                        @if($latestSubmission)
                            <div class="mb-4 text-sm text-gray-600">
                                Latest submission status: <strong>{{ ucfirst(str_replace('_', ' ', $latestSubmission->status)) }}</strong>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-3">
                                <form action="{{ route('submissions.updateStatus', $latestSubmission) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="button-main w-full">Approve Work</button>
                                </form>
                                <form action="{{ route('submissions.updateStatus', $latestSubmission) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="revision_requested">
                                    <button type="submit" class="button-main w-full bg-gray-500 hover:bg-gray-600">Request Revision</button>
                                </form>
                            </div>
                        @endif
                        <form action="{{ route('payments.release', $job) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="button-main w-full">Release Payment</button>
                        </form>
                    </div>

                    <div class="mt-6 p-6 bg-white rounded-lg shadow-md border border-line">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <h6 class="heading6">Received Proposals</h6>
                            <span class="text-sm text-secondary">{{ $job->proposals->count() }} application(s)</span>
                        </div>

                        @if($job->proposals->isEmpty())
                            <p class="text-sm text-secondary">No freelancer has applied to this job yet.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($job->proposals as $proposal)
                                    <div class="border border-line rounded-lg p-4">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div>
                                                <div class="font-bold text-title">{{ $proposal->freelancer->name }}</div>
                                                <div class="text-sm text-secondary">{{ $proposal->freelancer->email }}</div>
                                                <div class="text-sm mt-2">
                                                    <span class="font-semibold">Bid:</span>
                                                    ${{ number_format((float) $proposal->bid_amount, 2) }}
                                                </div>
                                                <div class="text-sm mt-1">
                                                    <span class="font-semibold">Applied:</span>
                                                    {{ $proposal->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="text-sm">
                                                @if($proposal->status === 'accepted')
                                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">Accepted</span>
                                                @elseif($proposal->status === 'rejected')
                                                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 font-semibold">Rejected</span>
                                                @else
                                                    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">Pending</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $proposal->cover_letter }}</div>

                                        <div class="mt-4 flex flex-wrap gap-3">
                                            @if($proposal->cv_file_url)
                                                <a href="{{ $proposal->cv_file_url }}" target="_blank" class="button-main bg-slate-600 hover:bg-slate-700">
                                                    View CV (PDF)
                                                </a>
                                            @endif
                                            <form action="{{ route('conversations.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="context_type" value="proposal">
                                                <input type="hidden" name="context_id" value="{{ $proposal->id }}">
                                                <button type="submit" class="button-main bg-gray-700 hover:bg-gray-800">Message Freelancer</button>
                                            </form>

                                            @if(!$job->accepted_proposal_id && $proposal->status === 'pending')
                                                <form action="{{ route('proposals.accept', $proposal) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="button-main">Accept Proposal</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <div class="mt-10 p-6 bg-blue-50 rounded-lg text-center">
                    <p class="text-blue-700">Please <a href="{{ route('login') }}" class="font-bold underline">Login</a> as a Freelancer to apply for this job.</p>
                </div>
            @endauth
        </div>

        <div class="sidebar w-full lg:w-1/3">
            <div class="p-6 bg-white rounded-lg shadow-md border border-line">
                <h6 class="heading6 mb-4">Contact Information</h6>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3">
                        <span class="ph ph-buildings text-xl text-primary"></span>
                        <span class="text-secondary">{{ $job->company_name ?? 'N/A' }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="ph ph-map-pin text-xl text-primary"></span>
                        <span class="text-secondary">{{ $job->job_location ?? 'N/A' }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="ph ph-envelope text-xl text-primary"></span>
                        <span class="text-secondary">{{ $job->email }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="ph ph-phone text-xl text-primary"></span>
                        <span class="text-secondary">{{ $job->phone_no }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="ph ph-link text-xl text-primary"></span>
                        <a href="{{ $job->url }}" target="_blank" class="text-blue-600 hover:underline">Application Link</a>
                    </li>
                </ul>
            </div>

            @auth
                @if($job->acceptedProposal && in_array(Auth::id(), [$job->employer_id, $job->acceptedProposal->freelancer_id], true))
                    <form action="{{ route('conversations.store') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="context_type" value="job">
                        <input type="hidden" name="context_id" value="{{ $job->id }}">
                        <button type="submit" class="button-main w-full text-center">Open Job Chat</button>
                    </form>
                @endif
            @endauth

            @auth
                @if($job->acceptedProposal && in_array(Auth::id(), [$job->employer_id, $job->acceptedProposal->freelancer_id], true))
                    <div class="p-6 bg-white rounded-lg shadow-md border border-line mt-6">
                        <h6 class="heading6 mb-4">Leave Review</h6>
                        <form action="{{ route('reviews.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <select name="rating" class="w-full border-gray-300 rounded-md">
                                <option value="">Select Rating</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} Star</option>
                                @endfor
                            </select>
                            <textarea name="comment" rows="3" class="w-full border-gray-300 rounded-md" placeholder="Write your review"></textarea>
                            <button type="submit" class="button-main w-full">Submit Review</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</section>
@endsection
