<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['shift_number', 'date', 'section'];

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
