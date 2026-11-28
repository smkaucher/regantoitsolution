<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A section of the Team page (e.g. "Software Engineer"), with the cover picture shown on its members' cards. */
class TeamGroup extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * The visible team for the website: [section name => [member arrays]] plus each section's cover,
     * in the order set in the admin. Hidden sections and hidden members are left out.
     *
     * @return array{groups: array<string, array<int, array>>, covers: array<string, ?string>}
     */
    public static function forSite(): array
    {
        $groups = [];
        $covers = [];
        foreach (static::active()->with(['members' => fn ($q) => $q->where('is_active', true)])->get() as $g) {
            if ($g->members->isEmpty()) {
                continue;
            }
            $groups[$g->name] = $g->members->map(fn (TeamMember $m) => $m->only([
                'name', 'designation', 'skills', 'photo', 'bio', 'experience', 'portfolio_url', 'linkedin_url', 'github_url', 'facebook_url',
            ]))->all();
            $covers[$g->name] = $g->cover;
        }

        return ['groups' => $groups, 'covers' => $covers];
    }
}
