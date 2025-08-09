<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Complexe;
use App\Models\User ;


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
}
