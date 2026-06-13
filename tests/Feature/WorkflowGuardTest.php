<?php

use App\Models\Gig;
use App\Models\GigCategory;
use App\Models\GigPackage;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows only open jobs on public jobs page', function () {
    $employer = User::factory()->create(['role' => 'employer']);

    Job::create([
        'employer_id' => $employer->id,
        'title' => 'Open Job',
        'categories' => 'Web',
        'deadline' => now()->addDays(5)->toDateString(),
        'url' => 'https://example.com/apply',
        'email' => 'hr@example.com',
        'phone_no' => '03001234567',
        'salary_type' => 'Hour',
        'min' => 10,
        'max' => 20,
        'description' => 'Open description',
        'status' => 'open',
    ]);

    Job::create([
        'employer_id' => $employer->id,
        'title' => 'Closed Job',
        'categories' => 'Web',
        'deadline' => now()->addDays(5)->toDateString(),
        'url' => 'https://example.com/apply2',
        'email' => 'hr2@example.com',
        'phone_no' => '03001234568',
        'salary_type' => 'Hour',
        'min' => 10,
        'max' => 20,
        'description' => 'Closed description',
        'status' => 'closed',
    ]);

    $response = $this->get(route('jobs.index'));
    $response->assertOk();
    $response->assertSee('Open Job');
    $response->assertDontSee('Closed Job');
});

it('prevents ordering a package from another gig', function () {
    $employer = User::factory()->create(['role' => 'employer']);
    $freelancer = User::factory()->create(['role' => 'candidate']);
    $category = GigCategory::create([
        'name' => 'Programming',
        'slug' => 'programming',
    ]);

    $gigA = Gig::create([
        'freelancer_id' => $freelancer->id,
        'gig_category_id' => $category->id,
        'title' => 'Gig A',
        'description' => 'desc',
        'status' => 'active',
    ]);
    $gigB = Gig::create([
        'freelancer_id' => $freelancer->id,
        'gig_category_id' => $category->id,
        'title' => 'Gig B',
        'description' => 'desc',
        'status' => 'active',
    ]);

    $packageB = GigPackage::create([
        'gig_id' => $gigB->id,
        'type' => 'basic',
        'name' => 'Basic',
        'description' => 'desc',
        'price' => 10,
        'delivery_days' => 2,
    ]);

    $response = $this->actingAs($employer)->post(route('orders.store', $gigA), [
        'gig_package_id' => $packageB->id,
        'payment_method' => 'easypaisa',
        'payer_name' => 'Test Employer',
        'payer_contact' => '03001234567',
        'transaction_reference' => 'TXN-123',
    ]);
    $response->assertSessionHas('error');
});
