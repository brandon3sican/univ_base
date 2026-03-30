<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'values',
        'years',
    ];

    protected $casts = [
        'values' => 'array',
        'years' => 'array',
    ];

    protected $table = 'sto_targets';

    public function stos()
    {
        return $this->hasMany(Sto::class);
    }
}
