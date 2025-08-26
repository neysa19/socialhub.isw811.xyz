<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    // Define los campos que se pueden asignar de forma masiva
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
    ];

    // Relación: Una publicación pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}