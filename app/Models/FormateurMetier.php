<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormateurMetier extends Model
{
    use HasFactory;

    protected $table = 'formateur_metier';

    protected $fillable = [
        'formateur_id',
        'metier_id'
    ];

    /**
     * Relation avec Formateur
     */
    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    /**
     * Relation avec Métier
     */
    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }
}