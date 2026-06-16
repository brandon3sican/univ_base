<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $table = 'ub_offices';

    protected $fillable = [
        'name',
        'office_types_id',
    ];

    public function officeType()
    {
        return $this->belongsTo(OfficeType::class);
    }
}
