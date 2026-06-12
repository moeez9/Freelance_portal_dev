<?php

namespace Database\Seeders;

use App\Models\GigCategory;
use App\Models\GigSubcategory;
use App\Models\GigServiceType;
use App\Models\JobCategory;
use App\Models\SalaryType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $gigCategories = [
            [
                'name' => 'Graphics & Design',
                'icon' => 'ph-palette',
                'subs' => [
                    'Logo Design' => ['Minimalist', '3D', 'Vintage'],
                    'Web Design' => ['UI/UX', 'Figma to HTML'],
                ],
            ],
            [
                'name' => 'Digital Marketing',
                'icon' => 'ph-megaphone',
                'subs' => [],
            ],
            [
                'name' => 'Writing & Translation',
                'icon' => 'ph-pencil-line',
                'subs' => [],
            ],
            [
                'name' => 'Video & Animation',
                'icon' => 'ph-video-camera',
                'subs' => [],
            ],
            [
                'name' => 'Music & Audio',
                'icon' => 'ph-music-notes',
                'subs' => [],
            ],
            [
                'name' => 'Programming & Tech',
                'icon' => 'ph-code',
                'subs' => [
                    'Web Development' => ['Full Website', 'Bug Fix', 'Landing Page'],
                    'Mobile Apps' => ['iOS', 'Android', 'Cross-platform'],
                ],
            ],
        ];

        foreach ($gigCategories as $cat) {
            $gigCategory = GigCategory::firstOrCreate(
                ['name' => $cat['name']],
                ['slug' => Str::slug($cat['name']), 'icon' => $cat['icon']]
            );

            foreach ($cat['subs'] as $subName => $serviceTypes) {
                $sub = GigSubcategory::firstOrCreate(
                    ['gig_category_id' => $gigCategory->id, 'name' => $subName],
                    ['slug' => Str::slug($subName)]
                );

                foreach ($serviceTypes as $serviceType) {
                    $serviceTypeAttrs = ['gig_subcategory_id' => $sub->id, 'name' => $serviceType];
                    if (Schema::hasColumn('gig_service_types', 'slug')) {
                        GigServiceType::firstOrCreate($serviceTypeAttrs, ['slug' => Str::slug($serviceType)]);
                    } else {
                        GigServiceType::firstOrCreate($serviceTypeAttrs);
                    }
                }
            }
        }

        $jobCategories = ['UX/UI Design', 'Mobile App', 'Web Design', 'Backend Dev', 'Other'];
        foreach ($jobCategories as $category) {
            JobCategory::firstOrCreate(
                ['name' => $category],
                ['slug' => Str::slug($category)]
            );
        }

        $salaryTypes = [
            ['name' => 'Hour', 'label' => 'Hourly'],
            ['name' => 'Day', 'label' => 'Day'],
            ['name' => 'Week', 'label' => 'Week'],
            ['name' => 'Month', 'label' => 'Month'],
            ['name' => 'Quarter', 'label' => 'Quarter'],
        ];
        foreach ($salaryTypes as $salaryType) {
            SalaryType::firstOrCreate(
                ['name' => $salaryType['name']],
                ['label' => $salaryType['label']]
            );
        }
    }
}
