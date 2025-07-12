<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zonas_encuentros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('capacidad');
            $table->string('responsable');
            $table->double('latitud');
            $table->double('longitud');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas_encuentros');
    }
};
