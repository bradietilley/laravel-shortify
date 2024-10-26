<?php

namespace BradieTilley\Shortify\Events;

use BradieTilley\Shortify\Models\ShortifyUrl;

class UrlExpired extends ShortifyEvent
{
    public function __construct(public readonly ShortifyUrl $url)
    {
    }
}
