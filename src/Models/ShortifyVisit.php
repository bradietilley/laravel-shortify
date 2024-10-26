<?php

namespace BradieTilley\Shortify\Models;

use BradieTilley\Shortify\ShortifyConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

/**
 * @property-read int $id
 * @property-read string $ulid
 *
 * @property string $shortify_url_id
 * @property int|string|null $user_id
 * @property ?string $ip
 * @property ?string $user_agent
 *
 * @property-read ?User $user
 * @property-read ShortifyUrl $url
 */
class ShortifyVisit extends Model
{
    public const UPDATED_AT = null;

    public $table = 'shortify_visits';

    protected $guarded = [];

    public $hidden = [
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(ShortifyConfig::getUserModel(), 'user_id');
    }

    public function url(): BelongsTo
    {
        return $this->belongsTo(ShortifyConfig::getShortUrlModel(), 'shortify_url_id');
    }
}
