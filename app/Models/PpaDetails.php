<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpaDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'column_order',
    ];

    public function children()
    {
        return $this->hasMany(PpaDetails::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(PpaDetails::class, 'parent_id');
    }

    public function recordType()
    {
        return $this->belongsTo(RecordType::class);
    }

    public function ppas()
    {
        return $this->hasMany(Ppa::class);
    }
}
