<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); // ФИО сотрудника
            $table->string('telegram_id')->unique(); // Telegram ID
            $table->enum('role', ['master', 'brigadier', 'operator', 'adjuster', 'admin']); // Роль
            $table->string('login')->nullable()->unique(); // Логин для админки
            $table->string('password')->nullable(); // Пароль для админки
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
