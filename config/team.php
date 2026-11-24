<?php

// Frontend-only team data (no database, no admin panel). Edit here to change the Team page.
$antu = [
    'name' => 'Antu Debnath',
    'skills' => 'Security Strategy, Penetration Testing, Risk Assessment',
    'photo' => 'assets/img/team/antu.webp',
    'github_url' => 'https://github.com/antudebnath-sec',
    'portfolio_url' => 'https://antudebnath-sec.github.io/',
];

return [
    // Cover picture shown at the top of every card in a section.
    'covers' => [
        'Author & Management Team' => 'assets/img/team/covers/author.webp',
        'Software Engineer' => 'assets/img/team/covers/software.webp',
        'Security Engineer Team' => 'assets/img/team/covers/security.webp',
        'Marketing & Designer Team' => 'assets/img/team/covers/marketing.webp',
    ],

    'groups' => [
        'Author & Management Team' => [
            ['name' => 'Adnan Sirat', 'designation' => 'CEO', 'skills' => 'Leadership, Strategy, Client Relations'],
            ['name' => 'Saiful', 'designation' => 'Team Lead', 'skills' => 'Team Management, Project Planning, Agile'],
            ['designation' => 'HR'] + $antu,
        ],
        'Software Engineer' => [
            ['name' => 'Kaucher', 'designation' => 'Full Stack Software Engineer', 'skills' => 'PHP, Laravel, Next.js, React, MySQL, REST APIs',
                'photo' => 'assets/img/team/kaucher.webp', 'github_url' => 'https://github.com/smkaucher', 'portfolio_url' => 'https://smkaucher.github.io/'],
            ['name' => 'Antor', 'designation' => 'Full Stack Software Engineer & Android Developer', 'skills' => 'Next.js, NestJS, React, Flutter, Java, Android',
                'photo' => 'assets/img/team/antor.webp', 'github_url' => 'https://github.com/AntorPi314/', 'portfolio_url' => 'https://antor.is-a.dev/',
                'linkedin_url' => 'https://www.linkedin.com/in/antor-hawlader'],
            ['name' => 'Tamim', 'designation' => 'Frontend Engineer & Flutter Developer', 'skills' => 'JavaScript, React, Flutter, UI Implementation',
                'photo' => 'assets/img/team/tamim.webp', 'github_url' => 'https://github.com/tamiie56', 'portfolio_url' => 'https://tamiie56.github.io/portfolio/',
                'linkedin_url' => 'https://www.linkedin.com/in/tamim-chad-likhon-206709389/'],
        ],
        'Security Engineer Team' => [
            ['designation' => 'Security Specialist & Lead'] + $antu,
            ['name' => 'Pranto Kumar Shil', 'designation' => 'Security Engineer', 'skills' => 'Vulnerability Assessment, Network Security, Incident Response',
                'portfolio_url' => 'https://pran0x.dev/'],
            ['name' => 'Siam Shikder Nahid', 'designation' => 'Security Engineer', 'skills' => 'Secure Code Review, OWASP, Threat Analysis'],
        ],
        'Marketing & Designer Team' => [
            ['name' => 'Shuvashish Biswas Shuvo', 'designation' => 'Digital Marketer', 'skills' => 'SEO, Google Ads, Meta Ads, Analytics', 'photo' => 'assets/img/team/shuvo.webp',
                'portfolio_url' => 'https://shuvashish-biswas-shuvo-digital-marketer.vercel.app/', 'linkedin_url' => 'https://bd.linkedin.com/in/shuvashish-biswas'],
            ['name' => 'Ruhul Amin', 'designation' => 'Social Media Analyst', 'skills' => 'Social Media Analytics, Content Strategy, Reporting', 'photo' => 'assets/img/team/ruhul-amin.webp'],
            ['name' => 'Saeed Ben Bishal', 'designation' => 'Graphics Designer', 'skills' => 'UI/UX Design, Graphic Design, Branding, Figma', 'photo' => 'assets/img/team/bishal.webp'],
        ],
    ],
];
