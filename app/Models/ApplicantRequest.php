<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantRequest extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'specialty_id', 'message', 'is_processed', 'ip',
    ];

    protected function casts(): array
    {
        return ['is_processed' => 'boolean'];
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }
}
