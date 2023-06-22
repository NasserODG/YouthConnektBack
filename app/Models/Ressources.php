<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressources extends Model
{
    use HasFactory;


    protected $table = 'ressources';

    // Liste des attributs pouvant être assignés massivement
    protected $fillable = ['titre', 'description','type', 'lien'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
