<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Наладчик
            $table->foreignId('equipment_id')->nullable()->constrained()->onDelete('cascade'); // Станок
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('cascade'); // Смена
            $table->dateTime('start_time')->nullable(); // Время начала наладки
            $table->dateTime('end_time')->nullable(); // Время завершения наладки
            $table->integer('duration')->nullable(); // Длительность в минутах
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adjustments');
    }
};
