<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoUniverse extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_ids',
        'values',
    ];

    protected $casts = [
        'office_ids' => 'array',
        'values' => 'array',
    ];

    protected $table = 'sto_universe';

    public function stos()
    {
        return $this->hasMany(Sto::class);
    }
}
