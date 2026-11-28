<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $guarded = [];

    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean'];

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    /**
     * Filter tabs this project shows under on the Projects page.
     * Web and software work also counts as UI/UX design; graphics and branding share one tab.
     * @return string[]
     */
    /**
     * The categories the site knows: tags() turns them into the Projects page tabs and config/service_work.php
     * shows them on the service pages. A category outside this list would show under "All" only.
     */
    public const CATEGORIES = ['Web Development', 'Software', 'UI/UX Design', 'Graphics Design', 'Branding'];

    public function tags(): array
    {
        return match ($this->category) {
            // Web, software and UI/UX share the same showcase.
            'Web Development', 'Software' => ['Web Development', 'Software', 'UI/UX Design'],
            'Graphics Design', 'Branding' => ['Graphics & Branding'],
            default => array_filter([$this->category]),
        };
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }
}
