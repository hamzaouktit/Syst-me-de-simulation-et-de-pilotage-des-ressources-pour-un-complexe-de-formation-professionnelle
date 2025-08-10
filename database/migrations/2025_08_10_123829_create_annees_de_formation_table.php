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
        Schema::create('annees_de_formation', function (Blueprint $table) {
            $table->id();
            $table->integer('annee')->unique()->comment('Année de formation (ex: 2024)');
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index('annee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annees_de_formation');
    }
};