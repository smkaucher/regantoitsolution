<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\TeamMember;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'counts' => [
                ['Services', Service::count(), 'services'],
                ['Projects', Project::count(), 'projects'],
                ['Team members', TeamMember::count(), 'team'],
                ['New requests', ServiceRequest::where('status', 'new')->count(), 'requests'],
            ],
            'latest' => ServiceRequest::with('service')->latest()->take(8)->get(),
        ]);
    }
}
