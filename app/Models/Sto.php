<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sto extends Model
{
    use HasFactory;

    protected $table = 'sto';

    protected $fillable = [
        'program_project_activity',
        'output_indicators',
        'office',
        'universe',
        'accomplishment',
        'remarks',
        'target_2024',
        'target_2025',
        'target_2026',
        'target_2027',
        'target_2028',
        'order_column',
        'parent_id',
        'record_type',
    ];

    protected $casts = [
        'order_column' => 'integer',
        'parent_id' => 'integer',
    ];

    public function children()
    {
        return $this->hasMany(Sto::class, 'parent_id')->orderBy('order_column');
    }

    public function parent()
    {
        return $this->belongsTo(Sto::class, 'parent_id');
    }

    public function getBaselineAttribute()
    {
        if ($this->universe !== null && $this->accomplishment !== null) {
            $universes = explode(',', $this->universe);
            $accomplishments = explode(',', $this->accomplishment);
            
            $baselines = [];
            foreach ($universes as $index => $universe) {
                $accomplishment = isset($accomplishments[$index]) ? $accomplishments[$index] : 0;
                $baselines[] = intval(trim($universe)) - intval(trim($accomplishment));
            }
            
            return implode(',', $baselines);
        }
        return null;
    }

    public function getTotalTargetsAttribute()
    {
        $targets = [];
        for ($year = 2024; $year <= 2028; $year++) {
            $field = "target_{$year}";
            if ($this->$field !== null) {
                $targets[] = $this->$field;
            }
        }
        return !empty($targets) ? implode(',', $targets) : null;
    }
}
