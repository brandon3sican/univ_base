<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
    ];

    public function ppaDetails()
    {
        return $this->hasMany(PpaDetails::class);
    }
}
