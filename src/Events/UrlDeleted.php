<?php

namespace BradieTilley\Shortify\Events;

use BradieTilley\Shortify\Models\ShortifyUrl;

class UrlDeleted extends ShortifyEvent
{
    public function __construct(public readonly ShortifyUrl $url)
    {
    }
}
