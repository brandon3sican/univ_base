<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function ppas()
    {
        return $this->hasMany(Ppa::class);
    }

    public function stos()
    {
        return $this->hasMany(Sto::class);
    }
}
