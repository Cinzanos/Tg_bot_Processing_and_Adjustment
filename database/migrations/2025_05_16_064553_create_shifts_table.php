<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_number')->nullable(); // Номер смены/бригады
            $table->date('date')->nullable(); // Дата смены
            $table->foreignId('section_id')->nullable()->constrained()->onDelete('restrict'); // Участок
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shifts');
    }
};
