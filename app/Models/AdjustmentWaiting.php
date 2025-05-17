<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustmentWaiting extends Model
{
    protected $fillable = ['equipment_id', 'shift_id', 'start_time', 'end_time', 'duration'];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
