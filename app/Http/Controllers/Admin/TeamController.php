<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamGroup;
use App\Models\TeamMember;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Admin > Team: the sections of the Team page (each with a cover picture) and the people in them.
 * Uploads go to storage under team/; only those are ever deleted (the original pictures under assets/ are kept).
 */
class TeamController extends Controller
{
    public function index()
    {
        return view('admin.team.index', ['groups' => TeamGroup::with('members')->orderBy('sort_order')->orderBy('id')->get()]);
    }

    // ---------- Sections ----------

    public function createGroup()
    {
        return view('admin.team.group', ['group' => new TeamGroup(['is_active' => true, 'sort_order' => (int) TeamGroup::max('sort_order') + 1])]);
    }

    public function storeGroup(Request $request)
    {
        $group = TeamGroup::create($this->groupAttributes($request));

        return redirect()->route('admin.team.index')->with('ok', "Section “{$group->name}” added. Now add the people in it.");
    }

    public function editGroup(TeamGroup $group)
    {
        return view('admin.team.group', ['group' => $group]);
    }

    public function updateGroup(Request $request, TeamGroup $group)
    {
        $group->update($this->groupAttributes($request, $group));

        return redirect()->route('admin.team.index')->with('ok', "Section “{$group->name}” saved.");
    }

    public function toggleGroup(TeamGroup $group)
    {
        $group->update(['is_active' => !$group->is_active]);

        return back()->with('ok', "“{$group->name}” is now " . ($group->is_active ? 'shown' : 'hidden') . ' on the Team page.');
    }

    public function destroyGroup(TeamGroup $group)
    {
        $this->deleteFile($group->cover);
        foreach ($group->members as $m) {
            $this->deleteFile($m->photo);
        }
        $group->delete(); // members cascade

        return redirect()->route('admin.team.index')->with('ok', "Section “{$group->name}” and its people deleted.");
    }

    private function groupAttributes(Request $request, ?TeamGroup $group = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('team_groups', 'name')->ignore($group)],
            'cover' => ['nullable', 'image', 'max:4096'],
            'remove_cover' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ], ['name.unique' => 'There is already a section with this name.']);

        $attrs = ['name' => trim($data['name']), 'sort_order' => $data['sort_order'] ?? 0, 'is_active' => $request->boolean('is_active')];
        if ($request->hasFile('cover')) {
            $this->deleteFile($group?->cover);
            $attrs['cover'] = ImageOptimizer::store($request->file('cover'), 'team/covers', 1200);
        } elseif ($request->boolean('remove_cover')) {
            $this->deleteFile($group?->cover);
            $attrs['cover'] = null;
        }

        return $attrs;
    }

    // ---------- People ----------

    public function createMember(Request $request)
    {
        $groupId = (int) $request->query('group') ?: TeamGroup::orderBy('sort_order')->value('id');
        $member = new TeamMember(['team_group_id' => $groupId, 'is_active' => true,
            'sort_order' => (int) TeamMember::where('team_group_id', $groupId)->max('sort_order') + 1]);

        return view('admin.team.member', ['member' => $member, 'groups' => TeamGroup::orderBy('sort_order')->get()]);
    }

    public function storeMember(Request $request)
    {
        $member = TeamMember::create($this->memberAttributes($request));

        return redirect()->route('admin.team.index')->with('ok', "{$member->name} added to “{$member->group->name}”.");
    }

    public function editMember(TeamMember $member)
    {
        return view('admin.team.member', ['member' => $member, 'groups' => TeamGroup::orderBy('sort_order')->get()]);
    }

    public function updateMember(Request $request, TeamMember $member)
    {
        $member->update($this->memberAttributes($request, $member));

        return redirect()->route('admin.team.index')->with('ok', "{$member->name} saved.");
    }

    public function toggleMember(TeamMember $member)
    {
        $member->update(['is_active' => !$member->is_active]);

        return back()->with('ok', "{$member->name} is now " . ($member->is_active ? 'shown' : 'hidden') . ' on the Team page.');
    }

    public function destroyMember(TeamMember $member)
    {
        $this->deleteFile($member->photo);
        $member->delete();

        return redirect()->route('admin.team.index')->with('ok', "{$member->name} removed from the team.");
    }

    private function memberAttributes(Request $request, ?TeamMember $member = null): array
    {
        $data = $request->validate([
            'team_group_id' => ['required', 'integer', 'exists:team_groups,id'],
            'name' => ['required', 'string', 'max:120'],
            'designation' => ['nullable', 'string', 'max:150'],
            'skills' => ['nullable', 'string', 'max:400'],
            'bio' => ['nullable', 'string', 'max:600'],
            'experience' => ['nullable', 'string', 'max:60'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'photo' => ['nullable', 'image', 'max:3072'],
            'remove_photo' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ], [
            'team_group_id.required' => 'Pick the section this person is in.',
            '*.url' => 'Enter a full link starting with https://',
        ]);

        $attrs = collect($data)->except(['photo', 'remove_photo'])
            ->map(fn ($v) => is_string($v) ? (trim($v) === '' ? null : trim($v)) : $v)->all();
        $attrs['sort_order'] = $data['sort_order'] ?? 0;
        $attrs['is_active'] = $request->boolean('is_active');
        if (isset($attrs['skills'])) { // "PHP ,Laravel,, React" -> "PHP, Laravel, React"
            $attrs['skills'] = collect(explode(',', $attrs['skills']))->map(fn ($s) => trim($s))->filter()->implode(', ') ?: null;
        }

        if ($request->hasFile('photo')) {
            $this->deleteFile($member?->photo);
            $attrs['photo'] = ImageOptimizer::store($request->file('photo'), 'team', 800);
        } elseif ($request->boolean('remove_photo')) {
            $this->deleteFile($member?->photo);
            $attrs['photo'] = null;
        }

        return $attrs;
    }

    /** Only pictures uploaded here (team/...) are deleted; each upload has its own file name, so no one else uses it. */
    private function deleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'team/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
