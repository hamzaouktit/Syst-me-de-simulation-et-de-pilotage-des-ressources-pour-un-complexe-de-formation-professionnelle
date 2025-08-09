<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Etablissement;
use App\Models\User;

class Complexe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'user_id',
    ];

    // Relation inverse 1-1 avec Utilisateur
    public function directeur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation 1-N : un complexe a plusieurs établissements
    public function etablissements()
    {
        return $this->hasMany(Etablissement::class);
    }
}
