@extends('layouts.app')
@section('content')
        @php
            $selectedCategoryId = old('job_category_id');
            $existingCategoryName = old('categories', $job->categories ?? '');

            if ($selectedCategoryId === null && $isEdit && $job) {
                $selectedCategoryId = $job->job_category_id;

                if (!$selectedCategoryId && $existingCategoryName !== '') {
                    $matchedCategory = $jobCategories->firstWhere('name', $existingCategoryName);

                    if ($matchedCategory) {
                        $selectedCategoryId = $matchedCategory->id;
                    } else {
                        $otherCategory = $jobCategories->firstWhere('name', 'Other');
                        $selectedCategoryId = $otherCategory?->id;
                    }
                }
            }

            $selectedCategoryModel = $selectedCategoryId !== null
                ? $jobCategories->firstWhere('id', (int) $selectedCategoryId)
                : null;

            $isOtherSelected = ($selectedCategoryModel?->name === 'Other');

            $otherCategoryValue = old('other_category');
            if ($otherCategoryValue === null && $isEdit && $isOtherSelected) {
                $otherCategoryValue = $job->categories ?? '';
            }

            $deadlineValue = old('deadline');
            if ($deadlineValue === null && $isEdit && !empty($job?->deadline)) {
                $deadlineValue = \Illuminate\Support\Carbon::parse($job->deadline)->toDateString();
            }

            $deadlineMin = now()->toDateString();
            if ($isEdit && !empty($deadlineValue) && $deadlineValue < $deadlineMin) {
                $deadlineMin = $deadlineValue;
            }
        @endphp
        <div class="dashboard_main overflow-hidden lg:w-screen lg:h-screen flex sm:pt-20 pt-16">
            <div class="menu_dashboard overflow-hidden flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white relative z-[2] max-lg:hidden">
                @include('partials.dashboard.employer-menu', ['active' => 'jobs_create'])
            </div>
            <div class="dashboard_service scrollbar_custom w-full bg-surface">
                <script>
                function setupImagePreview(inputId, previewId) {
                    const input = document.getElementById(inputId);
                    const preview = document.getElementById(previewId);
                    if (!input || !preview) {
                        return;
                    }
                    input.addEventListener('change', function() {
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = e => {
                                preview.src = e.target.result;
                                preview.classList.remove('hidden');

                                const bg = preview.closest('.bg_img');
                                const icon = bg ? bg.querySelector('span.ph') : null;
                                if (icon) icon.classList.add('hidden');
                            };
                            reader.readAsDataURL(input.files[0]);
                        }
                    });
                }

                document.addEventListener('DOMContentLoaded', function () {
                    setupImagePreview('uploadLogo', 'previewLogo');
                    setupImagePreview('uploadBanner', 'previewBanner');

                    const form = document.getElementById('job-form');
                    const minInput = document.getElementById('minSalary');
                    const maxInput = document.getElementById('maxSalary');

                    const categorySelect = document.getElementById('jobCategorySelect');
                    const categoryTextInput = document.getElementById('categoriesTextInput');
                    const otherWrap = document.getElementById('categoryOtherWrapper');
                    const otherInput = document.getElementById('categoryOtherInput');

                    const salarySelect = document.getElementById('salaryTypeSelect');
                    const salaryTextInput = document.getElementById('salaryTypeTextInput');

                    const syncCategory = () => {
                        if (!categorySelect) return;
                        const selected = categorySelect.options[categorySelect.selectedIndex];
                        const selectedName = selected ? (selected.dataset.name || '') : '';
                        if (categoryTextInput) categoryTextInput.value = selectedName;

                        const isOther = selectedName === 'Other';
                        if (otherWrap) otherWrap.classList.toggle('hidden', !isOther);
                        if (otherInput) {
                            otherInput.required = isOther;
                            otherInput.disabled = !isOther;
                            if (!isOther) {
                                otherInput.value = '';
                            }
                        }
                    };

                    const syncSalary = () => {
                        if (!salarySelect || !salaryTextInput) return;
                        const selected = salarySelect.options[salarySelect.selectedIndex];
                        salaryTextInput.value = selected ? (selected.dataset.name || '') : '';
                    };

                    categorySelect?.addEventListener('change', syncCategory);
                    salarySelect?.addEventListener('change', syncSalary);
                    syncCategory();
                    syncSalary();

                });
                </script>
                <form id="job-form" class="container h-fit lg:pt-15 lg:pb-30 max-lg:py-12 max-sm:py-8" method="POST" action="{{ $isEdit ? route('employer.jobs.update', $job) : route('employer.jobs.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if ($isEdit)
                    @method('PUT')
                    @endif
                    <button class="btn_open_popup btn_menu_dashboard flex items-center gap-2 lg:hidden" data-type="menu_dashboard">
                        <span class="ph ph-squares-four text-xl"></span>
                        <strong class="text-button">Menu</strong>
                    </button>
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h4 class="heading4 max-lg:mt-3">{{ $isEdit ? 'Edit Job' : 'Submit Job' }}</h4>
                        <button class="button-main" type="submit">{{ $isEdit ? 'Update Changes' : 'Save & Publish' }}</button>
                        @if($isEdit)
                        <a href="{{ route('employer.jobs.index') }}" class="button-main bg-gray-500 hover:bg-gray-600">Cancel</a>
                        @endif
                    </div>
                    <div class="infomation p-8 mt-7.5 rounded-lg bg-white">
                        <h5 class="heading5">Infomation</h5>
                        <div class="form grid sm:grid-cols-2 gap-5 mt-5">
                            <div class="upload_image">
                            <label for="uploadLogo">Upload Logo: <span class="text-red">*</span></label>
                            <div class="flex flex-wrap items-center gap-4 mt-3">
                        <!-- Image preview box -->
                     <div class="bg_img relative w-40 h-30 rounded-lg overflow-hidden border border-dashed border-line bg-surface">
                    <img id="previewLogo"
                 src="{{ $isEdit ? ($job->upload_logo_url ?? '') : '' }}"
                 class="upload_img w-full h-full object-cover {{ $isEdit && $job->upload_logo ? '' : 'hidden' }}"
                 alt="Logo Preview">
                    @if(!($isEdit && $job->upload_logo))
                <span class="ph ph-image text-5xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-secondary"></span>
                @endif
                </div>

                <div>
                <strong class="text-button">Upload a cover services:</strong>
                <p class="caption1 text-secondary mt-1">JPG 320x240px</p>
                <div class="upload_file flex items-center gap-3 w-[220px] mt-3 px-3 py-2 border border-line rounded">
                <label for="uploadLogo" class="caption2 py-1 px-3 rounded bg-line cursor-pointer">Choose File</label>
                <input type="file" name="upload_logo" id="uploadLogo" accept="image/*" class="caption2 cursor-pointer hidden"/>
                </div>
                </div>
                </div>
                </div>

                    <div class="upload_image mt-5">
                        <label for="uploadBanner">Upload Banner: <span class="text-red">*</span></label>
                         <div class="flex flex-wrap items-center gap-4 mt-3">
                            <!-- Banner preview box -->
                        <div class="bg_img relative w-40 h-30 rounded-lg overflow-hidden border border-dashed border-line bg-surface">
                            <img id="previewBanner"
                 src="{{ $isEdit ? ($job->upload_banner_url ?? '') : '' }}"
                 class="upload_img w-full h-full object-cover {{ $isEdit && $job->upload_banner ? '' : 'hidden' }}"
                 alt="Banner Preview">
                        @if(!($isEdit && $job->upload_banner))
                        <span class="ph ph-image text-5xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-secondary"></span>
                            @endif
                             </div>

                 <div>
                             <strong class="text-button">Upload a cover services:</strong>
                             <p class="caption1 text-secondary mt-1">JPG 1280x240px</p>
                                <div class="upload_file flex items-center gap-3 w-[220px] mt-3 px-3 py-2 border border-line rounded">
                                        <label for="uploadBanner" class="caption2 py-1 px-3 rounded bg-line cursor-pointer">Choose File</label>
                                        <input type="file" name="upload_banner" id="uploadBanner" accept="image/*" class="caption2 cursor-pointer hidden"/>
                                </div>
                            </div>
                        </div>
                    </div>
                            <div class="title">
                                <label for="title">Title: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" id="title" type="text" name="title" placeholder="Title..." value="{{ old('title', $job->title ?? '') }}" required />
                            </div>


                        {{-- <div class="type">
                                <label>Type: <span class="text-red">*</span></label>
                                <div class="select_block flex items-center w-full h-12 pr-10 pl-4 mt-2 border border-line rounded-lg">
                                    <div class="select">
                                       <span class="selected-text flex-1 min-w-0 truncate text-gray-800">Select option</span>
                                        <input type="hidden" name="type" id="typeInput"  value="{{ old('type', $job->type ?? '') }}">
                                        <ul class="list_option scrollbar_custom w-full max-h-[200px] bg-white">
                                            <li class="" data-item="Remote" {{ (old('type', $job->type ?? '') == 'Remote') ? 'selected' : '' }}>Remote</li>
                                            <li class="" data-item="Parttime" {{ (old('type', $job->type ?? '') == 'Parttime') ? 'selected' : '' }}>Parttime</li>
                                            <li class="" data-item="Fulltime"  {{ (old('type', $job->type ?? '') == 'Fulltime') ? 'selected' : '' }}>Fulltime</li>
                                        </ul>
                                    </div>
                                    <span class="icon_down ph ph-caret-down right-3"></span>
                                </div>
                            </div> --}}
                            <div class="categories">
                                <label for="jobCategorySelect">Categories: <span class="text-red-500">*</span></label>
                                <select id="jobCategorySelect" name="job_category_id" class="w-full h-12 px-4 mt-2 border border-line rounded-lg bg-white">
                                    <option value="">Select option</option>
                                    @foreach($jobCategories as $category)
                                        <option
                                            value="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            @selected((string) $selectedCategoryId === (string) $category->id)
                                        >
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="categories" id="categoriesTextInput" value="{{ old('categories', $selectedCategoryModel?->name ?? $existingCategoryName) }}">
                            </div>
                            <div class="category_other mt-4 {{ $isOtherSelected ? '' : 'hidden' }}" id="categoryOtherWrapper">
                                <label for="categoryOtherInput">Other <span class="text-red-500">*</span></label>
                                <input
                                    id="categoryOtherInput"
                                    class="w-full h-12 px-4 mt-2 border border-line rounded-lg bg-white"
                                    type="text"
                                    name="other_category"
                                    placeholder="Please Specify"
                                    value="{{ $otherCategoryValue }}"
                                    {{ $isOtherSelected ? '' : 'disabled' }}
                                >
                            </div>
                            <div class="deadline_date">
                                <label for="deadlineDate">Application deadline date: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg uppercase" id="deadlineDate" min="{{ $deadlineMin }}" name="deadline" type="date" value="{{ $deadlineValue }}" required />
                            </div>
                            <div class="external_url">
                                <label for="externalUrl">External URL for apply job: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" id="externalUrl" name="url" value="{{ old('url', $isEdit ? $job->url : '') }}" type="text" required />
                            </div>
                            <div class="job_apply_email">
                                <label for="jobApplyEmail">Job Apply Email: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" id="jobApplyEmail" name="email" type="text" value="{{ old('email', $isEdit ? $job->email : '') }}" required />
                            </div>
                            <div class="phone_number">
                                <label for="phoneNumber">Phone Number: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" id="phoneNumber" name="phone_no" value="{{ old('phone_no', $isEdit ? $job->phone_no : '') }}" type="text" required />
                            </div>
                            <div class="salary_type">
                                <label for="salaryTypeSelect">Salary type: <span class="text-red">*</span></label>
                                <select id="salaryTypeSelect" name="salary_type_id" class="w-full h-12 px-4 mt-2 border border-line rounded-lg bg-white">
                                    <option value="">Select option</option>
                                    @foreach($salaryTypes as $salaryType)
                                        <option
                                            value="{{ $salaryType->id }}"
                                            data-name="{{ $salaryType->name }}"
                                            @selected((string) old('salary_type_id', $job->salary_type_id ?? '') === (string) $salaryType->id)
                                        >
                                            {{ $salaryType->label ?? $salaryType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="salary_type" id="salaryTypeTextInput" value="{{ old('salary_type', $job->salary_type ?? '') }}">
                            </div>
                            <div class="min_salary">
                                <label for="minSalary">Min. salary: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" id="minSalary" name="min" value="{{ old('min', $job->min ?? '') }}" type="number" required />
                            </div>
                            <div class="max_salary">
                                <label for="maxSalary">Max. salary: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" id="maxSalary" name="max" value="{{ old('max', $job->max ?? '') }}" type="number" required />
                            </div>
                            <div class="desc col-span-full">
                                <label>Decscription: <span class="text-red">*</span></label>
                                <textarea class="w-full h-32 px-4 mt-2 border-line rounded-lg" type="text" name="description" required>{{ old('description', $job->description ?? '') }}</textarea>
                            </div>
                            <div class="desc col-span-full">
                                <label>Requirements: <span class="text-red">*</span></label>
                                <textarea class="w-full h-28 px-4 mt-2 border-line rounded-lg" name="requirements" required>{{ old('requirements', $job->requirements ?? '') }}</textarea>
                            </div>
                            <div class="required_skills col-span-full">
                                <label>Required Skills: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" type="text" name="required_skills" placeholder="e.g. Laravel, MySQL, REST API" value="{{ old('required_skills', $job->required_skills ?? '') }}" required />
                            </div>
                            <div class="company_name">
                                <label for="companyName">Company Name: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" id="companyName" name="company_name" type="text" value="{{ old('company_name', $job->company_name ?? '') }}" required />
                            </div>
                            <div class="job_location">
                                <label for="jobLocation">Job Location: <span class="text-red">*</span></label>
                                <input class="w-full h-12 px-4 mt-2 border-line rounded-lg" id="jobLocation" name="job_location" type="text" value="{{ old('job_location', $job->job_location ?? '') }}" required />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal">
            <div class="modal_item menu_dashboard -modal overflow-hidden relative flex-shrink-0 min-[320px]:w-[280px] w-[80vw] h-full bg-white" data-type="menu_dashboard">
                @include('partials.dashboard.employer-menu', ['active' => 'jobs_create'])
            </div>
        </div>
@endsection



