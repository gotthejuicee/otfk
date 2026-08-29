<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'document_category_id', 'title', 'file_path', 'external_url',
        'description', 'published_at', 'sort_order', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->external_url) {
            return $this->external_url;
        }

        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /** Розширення файлу великими літерами (PDF, DOCX) — бейдж у списку документів */
    public function getFileExtensionAttribute(): ?string
    {
        $source = $this->file_path ?: $this->external_url;

        if (! $source) {
            return null;
        }

        $path = $this->file_path ?: (parse_url($source, PHP_URL_PATH) ?: '');
        $ext = strtoupper(pathinfo($path, PATHINFO_EXTENSION));

        return $ext !== '' ? $ext : null;
    }

    /**
     * Розмір локального файлу, вже відформатований («1,2 МБ»).
     * Для зовнішніх посилань і відсутніх файлів — null (у полях БД розміру немає).
     */
    public function getFileSizeLabelAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($this->file_path)) {
            return null;
        }

        $bytes = $disk->size($this->file_path);

        if ($bytes >= 1048576) {
            return str_replace('.', ',', (string) round($bytes / 1048576, 1)) . ' МБ';
        }

        return max(1, (int) round($bytes / 1024)) . ' КБ';
    }
}
