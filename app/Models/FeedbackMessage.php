<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'is_read', 'ip'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
