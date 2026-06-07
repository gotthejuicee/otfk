<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    protected $table = 'staff';

    public const CATEGORIES = [
        'administration' => 'Адміністрація',
        'teacher' => 'Викладач',
    ];

    protected $fillable = [
        'full_name', 'position', 'category', 'department_id', 'photo',
        'email', 'phone', 'bio', 'academic_degree', 'sort_order', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('full_name');
    }

    public function scopeAdministration($query)
    {
        return $query->where('category', 'administration');
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/u', trim($this->full_name ?? ''));

        return mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1) . mb_substr($parts[1] ?? '', 0, 1));
    }
}
