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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('camera_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // El usuario que reporta

            // Detalles del reporte
            $table->string('type'); // Ej: 'Sin Señal', 'Vandalismo'
            $table->text('description')->nullable();
            $table->enum('priority', ['baja', 'media', 'alta', 'critica'])->default('media');

            // Estado del flujo (para seguimiento del supervisor)
            $table->enum('status', ['pendiente', 'en_revision', 'resuelto', 'cerrado'])->default('pendiente');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
