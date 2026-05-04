<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gass extends Model
{
    protected $table = 'gass';
    
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

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}
