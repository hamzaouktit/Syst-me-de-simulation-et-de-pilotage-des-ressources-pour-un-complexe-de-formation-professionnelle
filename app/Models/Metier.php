<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metier extends Model
{
    use HasFactory;

    protected $table = 'metiers';

    protected $fillable = [
        'nom',
        'description'
    ];

    /**
     * Relation many-to-many avec Module via la table pivot metier_module
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'metier_module', 'metier_id', 'module_id')
                    ->withTimestamps();
    }

    /**
     * Relation many-to-many avec Formateur via la table pivot formateur_metier
     */
    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'formateur_metier', 'metier_id', 'formateur_id')
                    ->withTimestamps();
    }

    /**
     * Obtenir tous les formateurs associés à ce métier
     */
    public function formateurMetiers()
    {
        return $this->hasMany(FormateurMetier::class, 'metier_id');
    }

    /**
     * Obtenir tous les modules associés à ce métier
     */
    public function metierModules()
    {
        return $this->hasMany(MetierModule::class, 'metier_id');
    }
}