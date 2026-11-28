<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin > Project requests: the briefs sent from the Contact page. */
class ProjectRequestController extends Controller
{
    public function index(Request $request)
    {
        // Only a known status filters; anything else (missing, unknown, or an array from ?status[]=) shows all.
        $status = $request->query('status');
        $status = is_string($status) && array_key_exists($status, ProjectRequest::STATUSES) ? $status : null;
        $items = ProjectRequest::query()
            ->when(
                $status !== null,
                fn ($q) => $q->where('status', $status),
                fn ($q) => $q->where('status', '!=', 'spam')
            )
            ->latest()->paginate(20)->withQueryString();

        $counts = ProjectRequest::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');

        return view('admin.requests.index', compact('items', 'status', 'counts'));
    }

    public function show(ProjectRequest $request)
    {
        // Step through the list without going back to it: spam only links to spam, the rest skip spam.
        $same = fn () => ProjectRequest::query()->when(
            $request->status === 'spam',
            fn ($q) => $q->where('status', 'spam'),
            fn ($q) => $q->where('status', '!=', 'spam')
        );

        return view('admin.requests.show', [
            'item' => $request,
            'newer' => $same()->where('id', '>', $request->id)->orderBy('id')->value('id'),
            'older' => $same()->where('id', '<', $request->id)->orderByDesc('id')->value('id'),
        ]);
    }

    public function update(Request $http, ProjectRequest $request)
    {
        $data = $http->validate(['status' => ['required', Rule::in(array_keys(ProjectRequest::STATUSES))]]);
        $request->update($data);

        return back()->with('ok', 'Status updated.');
    }

    public function destroy(ProjectRequest $request)
    {
        $request->delete();

        return redirect()->route('admin.requests.index')->with('ok', 'Request deleted.');
    }
}
