<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('adjustment_waitings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->nullable()->constrained()->onDelete('cascade'); // Станок
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('cascade'); // Смена
            $table->dateTime('start_time')->nullable(); // Время начала ожидания
            $table->dateTime('end_time')->nullable(); // Время завершения ожидания
            $table->integer('duration')->nullable(); // Длительность в минутах
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adjustment_waitings');
    }
};
