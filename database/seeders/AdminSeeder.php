<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'full_name' => 'Админ Админович',
            'telegram_id' => 'admin_000000', // Заглушка, замените на реальный Telegram ID, если нужно
            'role' => 'admin',
            'login' => 'admin',
            'password' => Hash::make('admin123'), // Пароль: admin123
        ]);
    }
}
