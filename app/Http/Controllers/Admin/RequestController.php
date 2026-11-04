<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    private const STATUSES = ['new', 'contacted', 'closed'];

    public function index(Request $request)
    {
        $status = in_array($request->query('status'), self::STATUSES, true) ? $request->query('status') : null;
        $service = $request->query('service');
        $q = trim((string) $request->query('q', ''));

        $requests = ServiceRequest::with('service')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($service, fn ($query) => $query->where('service_id', $service))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(fn ($w) => $w->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('company', 'like', $like)
                    ->orWhere('message', 'like', $like));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.requests.index', [
            'requests' => $requests,
            'services' => Service::orderBy('sort_order')->get(['id', 'title']),
            'counts' => ServiceRequest::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
            'filters' => ['status' => $status, 'service' => $service, 'q' => $q],
        ]);
    }

    public function show(ServiceRequest $serviceRequest)
    {
        return view('admin.requests.show', ['r' => $serviceRequest->load('service')]);
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', self::STATUSES),
            'admin_note' => 'nullable|string|max:3000',
        ]);

        $serviceRequest->update($data);

        return redirect()->route('admin.requests.show', $serviceRequest)->with('ok', 'Request updated.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $serviceRequest->delete();

        return redirect()->route('admin.requests.index')->with('ok', 'Request deleted.');
    }
}
