<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sto extends Model
{
    use HasFactory;

    protected $table = 'sto';

    protected $fillable = [
        'ppa_id',
        'indicator_id',
        'universe_id',
        'accomplishment_id',
        'targets_id',
    ];

    protected $casts = [
        'universe_id' => 'array',
        'accomplishment_id' => 'array',
        'targets_id' => 'array',
    ];

    public function ppa()
    {
        return $this->belongsTo(Ppa::class);
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    // Note: universe_id, accomplishment_id, and targets_id are JSON arrays
// Use direct queries to access related records
}
