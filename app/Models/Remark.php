<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    protected $fillable = ['user_id', 'equipment_id', 'shift_id', 'text', 'photo', 'type'];

    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'acceptance' => 'Прием смены',
            'handover' => 'Сдача смены',
            default => 'Неизвестно',
        };
    }

    public static function types(): array
    {
        return [
            'acceptance' => 'Прием смены',
            'handover' => 'Сдача смены',
        ];
    }

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
