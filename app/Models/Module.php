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
        'formation_id',
    ];

    protected $casts = [
        'masse_horaire' => 'integer',
    ];

    /**
     * Relation avec Formation
     */
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * Relation avec Métier (Many-to-Many)
     */
    public function metiers()
    {
        return $this->belongsToMany(Metier::class, 'metier_modules');
    }
}