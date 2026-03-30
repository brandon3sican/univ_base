<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppa extends Model
{
    use HasFactory;

    protected $table = 'ppa';

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
}
