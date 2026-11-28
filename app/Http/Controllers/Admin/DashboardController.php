<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'counts' => [
                ['New project requests', \App\Models\ProjectRequest::where('status', 'new')->count(), route('admin.requests.index', ['status' => 'new'])],
                ['Reviews waiting to be checked', \App\Models\Testimonial::waiting()->count(), route('admin.content.index', 'testimonials')],
                ['Services', Service::count(), route('admin.services.index')],
                ['Projects', Project::count(), route('admin.projects.index')],            ],
        ]);
    }
}
