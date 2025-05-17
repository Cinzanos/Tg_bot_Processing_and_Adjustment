<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('processings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Оператор
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade'); // Станок
            $table->foreignId('shift_id')->constrained()->onDelete('cascade'); // Смена
            $table->dateTime('start_time'); // Время начала обработки
            $table->dateTime('end_time')->nullable(); // Время завершения обработки
            $table->integer('duration')->nullable(); // Длительность в минутах
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('processings');
    }
};
