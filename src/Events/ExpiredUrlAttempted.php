<?php

namespace BradieTilley\Shortify\Events;

use BradieTilley\Shortify\Models\ShortifyUrl;
use Illuminate\Foundation\Auth\User;

class ExpiredUrlAttempted extends ShortifyEvent
{
    public function __construct(
        public readonly ShortifyUrl $url,
        public readonly ?User $user,
    ) {
    }
}
