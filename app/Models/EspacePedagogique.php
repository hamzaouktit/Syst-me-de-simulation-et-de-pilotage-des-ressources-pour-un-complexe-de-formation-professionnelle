<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspacePedagogique extends Model
{

    use HasFactory;

    protected $table = 'espaces_pedagogiques';

    protected $fillable = [
        'nom',
        'type',
        'capacite',
        'couvertureHoraireMax',
        'etablissement_id'
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }
}