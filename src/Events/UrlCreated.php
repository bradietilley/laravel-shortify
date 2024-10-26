<?php

namespace BradieTilley\Shortify\Events;

use BradieTilley\Shortify\Models\ShortifyUrl;

class UrlCreated extends ShortifyEvent
{
    public function __construct(public readonly ShortifyUrl $url)
    {
    }
}
