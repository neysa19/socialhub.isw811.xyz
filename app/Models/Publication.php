<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publication extends Model
{
    protected $fillable = [
        'user_id','title','content','image_path',
        'mode','scheduled_at','status','meta',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'meta'         => 'array',
    ];

    public function targets(): HasMany
    {
        return $this->hasMany(PostTarget::class);
    }
}