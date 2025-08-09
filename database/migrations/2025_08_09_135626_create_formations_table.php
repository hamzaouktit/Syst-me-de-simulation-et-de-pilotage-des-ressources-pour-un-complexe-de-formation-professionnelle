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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('niveau');
            $table->enum('type', ['initiale', 'continue', 'alternance', 'distance']);
            $table->unsignedBigInteger('etablissement_id');
            $table->timestamps();

            // Clé étrangère
            $table->foreign('etablissement_id')->references('id')->on('etablissements')->onDelete('cascade');
            
            // Index pour améliorer les performances
            $table->index('etablissement_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};