<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Project;
use App\Models\Service;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'counts' => [
                ['Services', Service::count(), route('admin.services.index')],
                ['Projects', Project::count(), route('admin.projects.index')],
                ['Blog articles', Blog::count(), route('admin.content.index', 'blogs')],
            ],
        ]);
    }
}
