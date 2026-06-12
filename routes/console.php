<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:audit-slugs', function () {
    $webRoutesPath = base_path('routes/web.php');
    $scanPaths = [
        base_path('routes/web.php'),
        base_path('app/Http/Controllers'),
        base_path('resources/views'),
    ];

    $slugRouteNames = [
        'jobs.show',
        'services.show',
        'employer.jobs.show',
        'employer.jobs.edit',
        'employer.jobs.update',
        'employer.jobs.destroy',
        'employer.jobs.updateStatus',
        'gigs.edit',
        'gigs.update',
        'gigs.status',
        'proposals.accept',
        'proposals.reject',
        'submissions.updateStatus',
        'orders.updateStatus',
        'notifications.read',
        'messages.show',
        'messages.latest',
        'messages.store',
    ];

    $issues = [];

    if (File::exists($webRoutesPath)) {
        $lines = preg_split('/\r\n|\r|\n/', File::get($webRoutesPath)) ?: [];
        foreach ($lines as $index => $line) {
            if (preg_match('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $line, $m)) {
                $param = $m[1];
                if (!Str::contains($line, '{' . $param . ':slug}') && !in_array($param, ['category', 'subcategory'], true)) {
                    $issues[] = [
                        'type' => 'Route Param',
                        'file' => 'routes/web.php:' . ($index + 1),
                        'detail' => 'Non-slug route parameter {' . $param . '} detected.',
                    ];
                }
            }
        }
    }

    $files = collect($scanPaths)->flatMap(function ($path) {
        if (File::isFile($path)) {
            return [$path];
        }
        if (File::isDirectory($path)) {
            return collect(File::allFiles($path))->map(fn ($f) => $f->getPathname())->all();
        }
        return [];
    })->filter(fn ($file) => Str::endsWith($file, ['.php', '.blade.php']))->values();

    foreach ($files as $file) {
        $relative = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);
        $lines = preg_split('/\r\n|\r|\n/', File::get($file)) ?: [];

        foreach ($lines as $index => $line) {
            foreach ($slugRouteNames as $routeName) {
                if (Str::contains($line, "route('{$routeName}'") || Str::contains($line, 'route("' . $routeName . '"')) {
                    if (preg_match('/route\(([^)]*)\)/', $line) && Str::contains($line, '->id')) {
                        $issues[] = [
                            'type' => 'Route Usage',
                            'file' => $relative . ':' . ($index + 1),
                            'detail' => "Possible ID usage for slug route '{$routeName}'.",
                        ];
                    }
                }
            }

            if (preg_match('/\/(job|gig|jobs|services|orders|proposals|notifications|messages)\/\{\$[^}]*id[^}]*\}/i', $line)) {
                $issues[] = [
                    'type' => 'URL Pattern',
                    'file' => $relative . ':' . ($index + 1),
                    'detail' => 'Hardcoded URL seems to include ID variable.',
                ];
            }
        }
    }

    if (empty($issues)) {
        $this->info('Slug audit passed. No obvious ID-based URL issues found.');
        return self::SUCCESS;
    }

    $this->error('Slug audit found ' . count($issues) . ' potential issue(s):');
    $this->table(
        ['Type', 'File', 'Detail'],
        array_map(fn ($i) => [$i['type'], $i['file'], $i['detail']], $issues)
    );

    return self::FAILURE;
})->purpose('Audit routes and code for non-slug URL patterns');
