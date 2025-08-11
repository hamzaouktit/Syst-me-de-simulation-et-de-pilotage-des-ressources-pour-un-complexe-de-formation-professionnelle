<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetierModule extends Model
{
    use HasFactory;

    protected $table = 'metier_module';

    protected $fillable = [
        'metier_id',
        'module_id'
    ];

    /**
     * Relation avec Métier
     */
    public function metier()
    {
        return $this->belongsTo(Metier::class);
    }

    /**
     * Relation avec Module
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}