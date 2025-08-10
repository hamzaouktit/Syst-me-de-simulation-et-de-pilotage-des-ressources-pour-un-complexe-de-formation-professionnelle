<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Groupe;

class AnneeDeFormation extends Model
{
    use HasFactory;

    protected $table = 'annees_de_formation';

    protected $fillable = [
        'annee'
    ];

    protected $casts = [
        'annee' => 'integer'
    ];

    // Relation avec les groupes
    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'annee_de_formation_id');
    }
}