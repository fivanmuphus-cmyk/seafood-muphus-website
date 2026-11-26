<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobOpening extends Model
{
    protected $fillable = [
        'type', 'position', 'hook', 'city', 'division', 'summary',
        'brochure_path', 'form_url', 'deadline', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['deadline' => 'date', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('deadline')->orWhereDate('deadline', '>=', today()));
    }

    public function typeLabel(): string
    {
        return $this->type === 'office' ? 'Office' : 'Outlet';
    }

    public function locationLabel(): string
    {
        return $this->type === 'office' ? ($this->division ?: 'Kantor pusat') : ($this->city ?: '');
    }

    /** Hanya tautan https yang boleh dipakai. */
    public function safeFormUrl(): ?string
    {
        return str_starts_with((string) $this->form_url, 'https://') ? $this->form_url : null;
    }
}