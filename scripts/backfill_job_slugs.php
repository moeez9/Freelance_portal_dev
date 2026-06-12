<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Job;
use Illuminate\Support\Str;

$jobs = Job::whereNull('slug')->orWhere('slug', '')->get();

foreach ($jobs as $job) {
    $job->slug = Str::slug($job->title) . '-' . Str::random(5);
    $job->save();
}

echo $jobs->count() . PHP_EOL;
