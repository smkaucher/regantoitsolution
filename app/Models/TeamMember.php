<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One person on the Team page, in one section. The same person can be added to more than one section. */
class TeamMember extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(TeamGroup::class, 'team_group_id');
    }
}
