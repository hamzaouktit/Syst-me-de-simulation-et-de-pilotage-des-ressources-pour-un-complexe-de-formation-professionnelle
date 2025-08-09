<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Relation avec Module
     * Une formation peut avoir plusieurs modules
     */
    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    /**
     * Relation avec les années de formation
     * Une formation peut avoir plusieurs années
     */
    public function anneesFormation()
    {
        return $this->hasMany(AnneeFormation::class);
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