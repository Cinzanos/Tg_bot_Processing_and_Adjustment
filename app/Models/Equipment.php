<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = ['section_id', 'machine_number'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function processings()
    {
        return $this->hasMany(Processing::class);
    }

    public function adjustments()
    {
        return $this->hasMany(Adjustment::class);
    }

    public function adjustmentWaitings()
    {
        return $this->hasMany(AdjustmentWaiting::class);
    }

    public function downtimes()
    {
        return $this->hasMany(Downtime::class);
    }

    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }
}
