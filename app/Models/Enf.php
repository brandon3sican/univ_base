<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enf extends Model
{
    protected $table = 'enf';

    protected $fillable = [
        'ppa_id',
        'indicator_id',
        'office_id',
        'universe',
        'accomplishment',
        'targets',
        'years',
        'remarks',
    ];

    protected $casts = [
        'office_id' => 'array',
        'universe' => 'array',
        'accomplishment' => 'array',
        'targets' => 'array',
        'years' => 'array',
    ];

    public function ppa(): BelongsTo
    {
        return $this->belongsTo(Ppa::class);
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }
}
