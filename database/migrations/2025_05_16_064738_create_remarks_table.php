<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Инициатор замечания
            $table->foreignId('equipment_id')->nullable()->constrained()->onDelete('cascade'); // Станок
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('cascade'); // Смена
            $table->text('text')->nullable(); // Текст замечания
            $table->string('photo')->nullable(); // Путь к фото
            $table->enum('type', ['acceptance', 'handover'])->nullable(); // Тип замечания
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('remarks');
    }
};
