<?php

namespace BradieTilley\Shortify\Events;

use Illuminate\Foundation\Auth\User;

class InvalidUrlAttempted extends ShortifyEvent
{
    public function __construct(
        public readonly string $url,
        public readonly ?User $user,
    ) {
    }
}
