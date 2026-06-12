<?php

return [
    'primary' => [
        ['label' => 'Home', 'route' => null, 'url' => '/'],
        ['label' => 'Jobs', 'route' => 'jobs.index'],
        ['label' => 'Services', 'route' => 'services.index'],
        ['label' => 'Messages', 'route' => 'messages.index', 'auth' => true],
    ],
    'candidate' => [
        ['label' => 'Dashboard', 'route' => 'candidate.dashboard'],
        ['label' => 'Orders', 'route' => 'candidate.orders'],
        ['label' => 'My Gigs', 'route' => 'candidate.services'],
        ['label' => 'Create Gig', 'route' => 'gigs.create'],
        ['label' => 'My Proposals', 'route' => 'candidate.proposals'],
    ],
    'employer' => [
        ['label' => 'Dashboard', 'route' => 'employer.dashboard'],
        ['label' => 'My Jobs', 'route' => 'employer.jobs.index'],
        ['label' => 'Post a Job', 'route' => 'employer.jobs.create'],
    ],
    'footer' => [
        'for_candidates' => [
            ['label' => 'Candidate Dashboard', 'route' => 'candidate.dashboard'],
            ['label' => 'My Orders', 'route' => 'candidate.orders'],
            ['label' => 'Browse Jobs', 'route' => 'jobs.index'],
            ['label' => 'My Proposals', 'route' => 'candidate.proposals'],
            ['label' => 'My Services', 'route' => 'candidate.services'],
        ],
        'for_employers' => [
            ['label' => 'Employer Dashboard', 'route' => 'employer.dashboard'],
            ['label' => 'Browse Services', 'route' => 'services.index'],
            ['label' => 'Submit Jobs', 'route' => 'employer.jobs.create'],
            ['label' => 'My Jobs', 'route' => 'employer.jobs.index'],
        ],
        'pages' => [
            ['label' => 'About Us', 'url' => '/about'],
            ['label' => 'Contact Us', 'url' => '/contact'],
            ['label' => 'Terms of Use', 'url' => '/terms'],
            ['label' => 'Login', 'route' => 'login'],
            ['label' => 'Register', 'route' => 'register'],
        ],
    ],
];
