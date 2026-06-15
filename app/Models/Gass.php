<?php

namespace App\Models;

use App\Traits\HasEditHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gass extends Model
{
    use HasEditHistory;

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
