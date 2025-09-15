<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Etablissement;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\AnneeDeFormation;

class Formation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'niveau',
        'type',
        'etablissement_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'string',
    ];

    /**
     * Relation avec Etablissement
     * Une formation appartient à un établissement
     */
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    /**
     * Relation avec Groupe
     * Une formation peut avoir plusieurs groupes
     */
    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }

    /**
     * Relation many-to-many avec Module via la table pivot formation_module
     * Une formation peut avoir plusieurs modules
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'formation_module', 'formation_id', 'module_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec les années de formation via les groupes
     * Une formation peut avoir plusieurs années via ses groupes
     */
    public function anneesFormation()
    {
        return $this->belongsToMany(
            AnneeDeFormation::class,
            'groupes',
            'formation_id',
            'annee_de_formation_id'
        )->distinct();
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par établissement
     */
    public function scopeByEtablissement($query, $etablissementId)
    {
        return $query->where('etablissement_id', $etablissementId);
    }

    /**
     * Accesseur pour formater le nom complet
     */
    public function getNomCompletAttribute()
    {
        return $this->titre . ' - ' . $this->niveau . ' (' . $this->type . ')';
    }
}