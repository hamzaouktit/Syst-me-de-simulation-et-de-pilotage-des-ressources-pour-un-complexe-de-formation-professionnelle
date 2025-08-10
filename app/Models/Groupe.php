<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AnneeDeFormation;
use App\Models\Formation;
use App\Models\Groupe;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'effectif',
        'formation_id',
        'annee_de_formation_id'
    ];

    protected $casts = [
        'effectif' => 'integer'
    ];

    // Relation avec Formation
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }











    // Relation avec AnneeDeFormation
    public function anneeDeFormation()
{
    return $this->belongsTo(AnneeDeFormation::class);
}


    // Scope pour filtrer par formation
    public function scopeByFormation($query, $formationId)
    {
        return $query->where('formation_id', $formationId);
    }

    // Scope pour filtrer par année
    public function scopeByAnnee($query, $anneeId)
    {
        return $query->where('annee_de_formation_id', $anneeId);
    }

    // Accesseur pour le nom complet du groupe
    public function getNomCompletAttribute()
    {
        return $this->nom . ' (' . $this->anneeDeFormation->annee . ')';
    }

    // Méthode pour vérifier l'unicité
    public static function existsForFormationAndAnnee($nom, $formationId, $anneeId, $excludeId = null)
    {
        $query = self::where('nom', $nom)
                    ->where('formation_id', $formationId)
                    ->where('annee_de_formation_id', $anneeId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}