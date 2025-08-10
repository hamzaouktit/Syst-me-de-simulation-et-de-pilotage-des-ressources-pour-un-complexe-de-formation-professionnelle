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
        Schema::create('groupes', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->comment('Nom du groupe');
            $table->integer('effectif')->default(0)->comment('Nombre d\'étudiants dans le groupe');
            
            // Clés étrangères
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->foreignId('annee_de_formation_id')->constrained('annees_de_formation')->onDelete('cascade');
            
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['formation_id', 'annee_de_formation_id']);
            $table->index('nom');
            
            // Contrainte d'unicité composite : un groupe unique par nom/formation/année
            $table->unique(['nom', 'formation_id', 'annee_de_formation_id'], 'unique_groupe_formation_annee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupes');
    }
};