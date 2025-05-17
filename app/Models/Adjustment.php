<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    protected $fillable = ['user_id', 'equipment_id', 'shift_id', 'start_time', 'end_time', 'duration'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
