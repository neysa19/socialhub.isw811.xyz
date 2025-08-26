<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishSchedule extends Model
{
    protected $table = 'publish_schedules';
    protected $fillable = ['user_id','weekday','time']; // <- NO 'day'

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}