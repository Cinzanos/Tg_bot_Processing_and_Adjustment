<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('downtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Инициатор простоя
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade'); // Станок
            $table->foreignId('shift_id')->constrained()->onDelete('cascade'); // Смена
            $table->dateTime('start_time'); // Время начала простоя
            $table->dateTime('end_time')->nullable(); // Время завершения простоя
            $table->integer('duration')->nullable(); // Длительность в минутах
            $table->enum('reason', ['reason1', 'reason2', 'reason3', 'reason4']); // Причина простоя
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('downtimes');
    }
};
