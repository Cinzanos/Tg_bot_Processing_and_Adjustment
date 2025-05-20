<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'Мастер',
            'Бригадир',
            'Оператор',
            'Наладчик',
            'Администратор',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
