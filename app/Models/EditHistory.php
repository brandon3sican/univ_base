<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditHistory extends Model
{
    protected $table = 'ub_edit_history';

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'action',
        'changes',
        'description',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getModelNameAttribute(): string
    {
        return class_basename($this->model_type);
    }
}
