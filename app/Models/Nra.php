<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nra extends Model
{
    use HasFactory;

    protected $table = 'nra';

    protected $fillable = [
        'program_project_activity',
        'output_indicators',
        'office',
        'universe',
        'accomplishment',
        'order_column',
        'parent_id',
        'record_type',
    ];

    protected $casts = [
        'order_column' => 'integer',
        'parent_id' => 'integer',
    ];

    public function getBaselineAttribute()
    {
        if ($this->universe !== null && $this->accomplishment !== null && 
            is_numeric($this->universe) && is_numeric($this->accomplishment)) {
            return intval($this->universe) - intval($this->accomplishment);
        }
        return null;
    }

    public function parent()
    {
        return $this->belongsTo(Nra::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Nra::class, 'parent_id')->orderBy('order_column');
    }

    public function getHierarchicalNumberAttribute()
    {
        if (!$this->parent_id) {
            // Root level (program)
            return '';
        }

        $parent = $this->parent;
        $siblings = $parent->children()->where('record_type', $this->record_type)->get();
        $position = $siblings->search(function ($item) {
            return $item->id === $this->id;
        }) + 1;

        if ($parent->parent_id) {
            // Sub-activity or deeper
            return $parent->hierarchical_number . '.' . $position;
        } else {
            // Direct child (project or activity)
            return $position;
        }
    }

    public function getIndentationLevelAttribute()
    {
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        return $level;
    }
}
