<?php

// Which projects appear as "Recent Work" on each service page.
// Key: service slug. Value: project categories (the Category field of a project in the admin panel).
// A service that is not listed here, or has no matching project, shows no Recent Work section.
return [
    'development' => ['Web Development', 'Software'],
    'design-branding' => ['UI/UX Design', 'Branding'],
    'digital-marketing' => ['Graphics Design'],
];
