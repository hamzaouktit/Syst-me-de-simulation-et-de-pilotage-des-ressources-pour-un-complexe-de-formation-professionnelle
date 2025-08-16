<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'longitude',
        'altitude',
        'complexe_id',
        'user_id',
    ];

    // Relation inverse avec Complexe
    public function complexe()
    {
        return $this->belongsTo(Complexe::class);
    }

    // Relation inverse 1-1 avec Utilisateur
    public function directeur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation avec Formation
    public function formations()
    {
        return $this->hasMany(Formation::class);
    }

    // Relation avec Formateur
    public function formateurs()
    {
        return $this->hasMany(Formateur::class);
    }

    // Relation avec EspacePedagogique
    public function espacesPedagogiques()
    {
        return $this->hasMany(EspacePedagogique::class);
    }
}