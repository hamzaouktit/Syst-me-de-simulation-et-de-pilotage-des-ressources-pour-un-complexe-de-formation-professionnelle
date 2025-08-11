<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'email',
        'masse_horaire_disponible',
        'etablissement_id'
    ];

    // Relation avec Etablissement
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    // Relation many-to-many avec Metier via la table pivot formateur_metier
    public function metiers()
    {
        return $this->belongsToMany(Metier::class, 'formateur_metier', 'formateur_id', 'metier_id')
                    ->withTimestamps();
    }

    // Pour obtenir les modules d'un formateur via les métiers
    public function modules()
    {
        return $this->hasManyThrough(
            Module::class,
            MetierModule::class,
            'metier_id', // Clé étrangère sur metier_module
            'id', // Clé locale sur modules
            'id', // Clé locale sur formateurs
            'module_id' // Clé étrangère sur metier_module
        )->join('formateur_metier', function($join) {
            $join->on('metier_module.metier_id', '=', 'formateur_metier.metier_id')
                 ->where('formateur_metier.formateur_id', $this->id);
        });
    }
}