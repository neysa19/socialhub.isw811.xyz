<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublishSchedule extends Model
{
     protected $table = 'publish_schedules';

    // Usamos 'day' y 'time' como en tu controlador
    protected $fillable = ['user_id', 'day', 'time'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}