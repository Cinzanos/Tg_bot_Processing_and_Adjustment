<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $fillable = ['full_name', 'telegram_id', 'role_id', 'login', 'password'];

    protected $hidden = ['password'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->name : 'Неизвестно';
    }

    public static function roles()
    {
        return Role::pluck('name', 'id')->toArray();
    }

    public function processings()
    {
        return $this->hasMany(Processing::class);
    }

    public function adjustments()
    {
        return $this->hasMany(Adjustment::class);
    }

    public function downtimes()
    {
        return $this->hasMany(Downtime::class);
    }

    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

    public function isAdmin()
    {
        return $this->role && $this->role->name === 'Администратор';
    }
}
