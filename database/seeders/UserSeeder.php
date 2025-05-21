<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Получаем роли, кроме администратора
        $roles = Role::where('name', '!=', 'Администратор')->get();

        foreach ($roles as $role) {
            // Количество пользователей для каждой роли
            $count = match ($role->name) {
                'Оператор' => 15,
                'Наладчик' => 10,
                'Бригадир' => 5,
                'Мастер' => 3,
            };

            for ($i = 0; $i < $count; $i++) {
                User::create([
                    'full_name' => $faker->name,
                    'telegram_id' => 'tg_' . $faker->unique()->numberBetween(200000, 999999),
                    'role_id' => $role->id,
                    'login' => null,
                    'password' => null,
                ]);
            }
        }
    }
}
