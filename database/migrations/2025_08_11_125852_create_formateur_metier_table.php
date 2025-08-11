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
        Schema::create('formateur_metier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formateur_id')->constrained('formateurs')->onDelete('cascade');
            $table->foreignId('metier_id')->constrained('metiers')->onDelete('cascade');
            $table->timestamps();
            
            // Clé composite pour éviter les doublons
            $table->unique(['formateur_id', 'metier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formateur_metier');
    }
};