<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'masse_horaire'
    ];

    /**
     * Relation many-to-many avec Formation via la table pivot formation_module
     */
    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'formation_module', 'module_id', 'formation_id')
                    ->withTimestamps();
    }

    /**
     * Relation many-to-many avec Metier via la table pivot metier_module
     */
    public function metiers()
    {
        return $this->belongsToMany(Metier::class, 'metier_module', 'module_id', 'metier_id')
                    ->withTimestamps();
    }

    /**
     * Obtenir tous les métiers associés à ce module
     */
    public function metierModules()
    {
        return $this->hasMany(MetierModule::class, 'module_id');
    }

    /**
     * Obtenir toutes les formations associées à ce module
     */
    public function formationModules()
    {
        return $this->hasMany(FormationModule::class, 'module_id');
    }
        // Dans le modèle Module
    public function belongsToEtablissement($etablissementId)
    {
        return $this->formations()->where('etablissement_id', $etablissementId)->exists();
    }
}