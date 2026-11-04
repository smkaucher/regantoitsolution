<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index()
    {
        return view('admin.team.index', ['members' => TeamMember::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function create()
    {
        return view('admin.team.form', ['member' => new TeamMember(['is_active' => true, 'sort_order' => (int) TeamMember::max('sort_order') + 1])]);
    }

    public function store(Request $request)
    {
        $member = TeamMember::create($this->attributes($request, $this->validated($request)));

        return redirect()->route('admin.team.edit', $member)->with('ok', 'Team member added.');
    }

    public function edit(TeamMember $team)
    {
        return view('admin.team.form', ['member' => $team]);
    }

    public function update(Request $request, TeamMember $team)
    {
        $team->update($this->attributes($request, $this->validated($request), $team));

        return redirect()->route('admin.team.edit', $team)->with('ok', 'Team member saved.');
    }

    public function toggle(TeamMember $team)
    {
        $team->update(['is_active' => !$team->is_active]);

        return back()->with('ok', $team->name . ($team->is_active ? ' is now visible.' : ' is now hidden.'));
    }

    /** Swap a member with its neighbour, then renumber so orders stay unique. */
    public function move(Request $request, TeamMember $team)
    {
        $dir = $request->input('dir') === 'up' ? -1 : 1;

        DB::transaction(function () use ($team, $dir) {
            $ids = TeamMember::orderBy('sort_order')->orderBy('id')->pluck('id')->all();
            $i = array_search($team->id, $ids, true);
            $j = $i + $dir;
            if ($i === false || !isset($ids[$j])) {
                return;
            }
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $pos => $id) {
                TeamMember::whereKey($id)->update(['sort_order' => $pos + 1]);
            }
        });

        return back();
    }

    public function destroy(TeamMember $team)
    {
        $this->deletePhoto($team->photo);
        $team->delete();

        return redirect()->route('admin.team.index')->with('ok', 'Team member deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'designation' => 'required|string|max:150',
            'bio' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:3072',
            'remove_photo' => 'nullable|boolean',
            'portfolio_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'nullable|boolean',
        ]);
    }

    private function attributes(Request $request, array $data, ?TeamMember $member = null): array
    {
        $attrs = [
            'name' => $data['name'],
            'designation' => $data['designation'],
            'bio' => $data['bio'] ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'facebook_url' => $data['facebook_url'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('photo')) {
            $this->deletePhoto($member?->photo);
            $attrs['photo'] = \App\Support\ImageOptimizer::store($request->file('photo'), 'team', 800);
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($member?->photo);
            $attrs['photo'] = null;
        }

        return $attrs;
    }

    /** Only files uploaded through this panel (under team/) are removed. */
    private function deletePhoto(?string $path): void
    {
        if ($path && str_starts_with($path, 'team/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
