<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'masse_horaire',
        'formation_id'
    ];

    /**
     * Relation avec Formation
     */
    public function formation()
    {
        return $this->belongsTo(Formation::class);
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
}