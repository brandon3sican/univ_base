<?php

namespace App\Models;

use App\Traits\HasEditHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppa extends Model
{
    use HasFactory, HasEditHistory;

    protected $table = 'ub_ppa';

    protected $fillable = [
        'name',
        'types_id',
        'record_type_id',
        'ppa_details_id',
        'indicator_id',
        'office_id',
    ];

    protected $casts = [
        'office_id' => 'array',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function recordType()
    {
        return $this->belongsTo(RecordType::class);
    }

    public function ppaDetails()
    {
        return $this->belongsTo(PpaDetails::class);
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function stos()
    {
        return $this->hasMany(Sto::class);
    }

    public function offices()
    {
        return $this->belongsToMany(Office::class, 'ub_ppa', 'id', 'office_id');
    }
}
