<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $guarded = [];

    protected $casts = ['published_at' => 'datetime', 'is_active' => 'boolean'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderByDesc('published_at');
    }
}
