<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'reviewed_at' => 'datetime'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    /** Reviews sent from the website that no admin has shown, hidden or edited yet. */
    public function scopeWaiting($q)
    {
        return $q->where('source', 'visitor')->whereNull('reviewed_at');
    }

    public function isWaiting(): bool
    {
        return $this->source === 'visitor' && $this->reviewed_at === null;
    }
}
