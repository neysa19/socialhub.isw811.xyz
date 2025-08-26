<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id','provider','provider_user_id','access_token','refresh_token','token_expires_at','scopes','meta'
    ];
    protected $casts = ['token_expires_at' => 'datetime', 'meta' => 'array'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
