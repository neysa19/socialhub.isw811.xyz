<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTarget extends Model
{
    // Si tu tabla se llama post_targets, no necesitas $table.
    // protected $table = 'post_targets';

    protected $fillable = [
        'publication_id',
        'provider',
        'status',
        'provider_post_id',
        'error',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }
}
