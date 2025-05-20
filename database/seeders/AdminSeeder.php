<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Получаем роль "Администратор"
        $adminRole = Role::where('name', 'Администратор')->firstOrFail();

        // Создаем генератор данных
        $faker = Faker::create();

        // Сколько админов создавать
        $count = 20;

        for ($i = 0; $i < $count; $i++) {
            User::create([
                'full_name' => $faker->name,
                'telegram_id' => 'tg_' . $faker->unique()->numberBetween(100000, 999999),
                'role_id' => $adminRole->id,
                'login' => $faker->unique()->userName,
                'password' => Hash::make('admin123'), // один и тот же пароль
            ]);
        }
    }
}
