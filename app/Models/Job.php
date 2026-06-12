<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $casts = [
        'deadline' => 'date',
        'closed_by_employer' => 'boolean',
    ];

    protected $fillable = [
        'employer_id',
        'upload_logo',
        'upload_banner',
        'title',
        'slug',
        // 'type',
        'categories',
        'job_category_id',
        'deadline',
        'url',
        'email',
        'phone_no',
        'salary_type',
        'salary_type_id',
        'min',
        'max',
        'description',
        'requirements',
        'required_skills',
        'company_name',
        'job_location',
        'status',
        'closed_by_employer',
        'accepted_proposal_id',
    ];

    //jobs belongs to a employer (client)
    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    protected static function booted()
    {
        static::creating(function (self $job) {
            $job->slug = static::generateUniqueTitleSlug($job->title);
        });

        static::updating(function (self $job) {
            if ($job->isDirty('title')) {
                $job->slug = static::generateUniqueTitleSlug($job->title, $job->id);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function closeExpiredOpenJobs(): void
    {
        if (!Schema::hasTable('jobs')) {
            return;
        }

        $query = static::query()
            ->where('status', 'open')
            ->whereDate('deadline', '<', Carbon::today());

        $updateData = ['status' => 'closed'];
        if (Schema::hasColumn('jobs', 'closed_by_employer')) {
            $updateData['closed_by_employer'] = false;
        }

        $query->update($updateData);
    }

    public function refreshStatusByDeadline(): void
    {
        if ($this->status !== 'open') {
            return;
        }

        if ($this->isDeadlinePassed()) {
            $updateData = ['status' => 'closed'];
            if (Schema::hasColumn('jobs', 'closed_by_employer')) {
                $updateData['closed_by_employer'] = false;
            }

            $this->update($updateData);
            $this->refresh();
        }
    }

    public function isDeadlinePassed(): bool
    {
        return Carbon::parse($this->deadline)->lt(Carbon::today());
    }

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function salaryType()
    {
        return $this->belongsTo(SalaryType::class, 'salary_type_id');
    }
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function acceptedProposal()
    {
        return $this->belongsTo(Proposal::class, 'accepted_proposal_id');
    }

    public function workSubmissions()
    {
        return $this->hasMany(WorkSubmission::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getUploadLogoUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl($this->upload_logo);
    }

    public function getUploadBannerUrlAttribute(): ?string
    {
        return $this->resolvePublicImageUrl($this->upload_banner);
    }

    private function resolvePublicImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalizedPath = ltrim($path, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            return asset($normalizedPath);
        }

        return asset('storage/' . $normalizedPath);
    }

    private static function generateUniqueTitleSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'job';
        }

        do {
            $slug = $base . '-' . Str::random(8);
            $exists = static::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists();
        } while ($exists);

        return $slug;
    }
}
