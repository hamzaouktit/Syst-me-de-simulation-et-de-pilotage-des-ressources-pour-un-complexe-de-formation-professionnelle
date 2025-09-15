<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormationModule extends Model
{
    use HasFactory;

    protected $table = 'formation_module';

    protected $fillable = [
        'formation_id',
        'module_id'
    ];

    /**
     * Relation avec Formation
     */
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * Relation avec Module
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}