<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'ub_types';

    protected $fillable = [
        'code',
        'desc',
    ];
}
