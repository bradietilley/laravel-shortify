<?php

namespace BradieTilley\Shortify\Events;

use BradieTilley\Shortify\Models\ShortifyUrl;
use BradieTilley\Shortify\Models\ShortifyVisit;

class UrlVisited extends ShortifyEvent
{
    public function __construct(
        public readonly ShortifyUrl $url,
        public readonly ShortifyVisit $visit,
    ) {
    }
}
