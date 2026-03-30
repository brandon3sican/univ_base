<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoAccomplishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_ids',
        'values',
        'remarks',
        'years',
    ];

    protected $casts = [
        'office_ids' => 'array',
        'values' => 'array',
        'remarks' => 'array',
        'years' => 'array',
    ];

    public function stos()
    {
        return $this->hasMany(Sto::class);
    }
}
