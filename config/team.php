<?php

// Frontend-only team data (no database, no admin panel). Edit here to change the Team page.
$antu = [
    'name' => 'Antu Debnath',
    'skills' => 'Security Strategy, Penetration Testing, Risk Assessment',
    'photo' => 'assets/img/team/antu.png',
    'github_url' => 'https://github.com/antudebnath-sec',
    'portfolio_url' => 'https://antudebnath-sec.github.io/',
];

return [
    // Cover picture shown at the top of every card in a section.
    'covers' => [
        'Author & Management Team' => 'assets/img/team/covers/author.jpg',
        'Software Engineer' => 'assets/img/team/covers/software.jpg',
        'Security Engineer Team' => 'assets/img/team/covers/security.jpg',
        'Marketing & Designer Team' => 'assets/img/team/covers/marketing.jpg',
    ],

    'groups' => [
        'Author & Management Team' => [
            ['name' => 'Adnan Sirat', 'designation' => 'CEO', 'skills' => 'Leadership, Strategy, Client Relations'],
            ['name' => 'Saiful', 'designation' => 'Team Lead', 'skills' => 'Team Management, Project Planning, Agile'],
            ['designation' => 'HR'] + $antu,
        ],
        'Software Engineer' => [
            ['name' => 'Kaucher', 'designation' => 'Full Stack Software Engineer', 'skills' => 'PHP, Laravel, Next.js, React, MySQL, REST APIs',
                'photo' => 'assets/img/team/kaucher.jpeg', 'github_url' => 'https://github.com/smkaucher', 'portfolio_url' => 'https://smkaucher.github.io/'],
            ['name' => 'Antor', 'designation' => 'Full Stack Software Engineer & Android Developer', 'skills' => 'Next.js, NestJS, React, Flutter, Java, Android',
                'photo' => 'assets/img/team/antor.jpeg'],
            ['name' => 'Tamim', 'designation' => 'Frontend Engineer & Flutter Developer', 'skills' => 'JavaScript, React, Flutter, UI Implementation',
                'photo' => 'assets/img/team/tamim.jpg'],
        ],
        'Security Engineer Team' => [
            ['designation' => 'Security Specialist & Lead'] + $antu,
            ['name' => 'Pranto Kumar Shil', 'designation' => 'Security Engineer', 'skills' => 'Vulnerability Assessment, Network Security, Incident Response'],
            ['name' => 'Siam Shikder Nahid', 'designation' => 'Security Engineer', 'skills' => 'Secure Code Review, OWASP, Threat Analysis'],
        ],
        'Marketing & Designer Team' => [
            ['name' => 'Shuvo', 'designation' => 'Digital Marketer', 'skills' => 'SEO, Google Ads, Meta Ads, Analytics', 'photo' => 'assets/img/team/shuvo.jpeg'],
            ['name' => 'Ruhul Amin', 'designation' => 'Social Media Analyst', 'skills' => 'Social Media Analytics, Content Strategy, Reporting'],
            ['name' => 'Bishal', 'designation' => 'Designer', 'skills' => 'UI/UX Design, Graphic Design, Branding, Figma'],
        ],
    ],
];
