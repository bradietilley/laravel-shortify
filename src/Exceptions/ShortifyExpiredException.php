<?php

namespace BradieTilley\Shortify\Exceptions;

use BradieTilley\Shortify\Models\ShortifyUrl;

class ShortifyExpiredException extends ShortifyException
{
    public function __construct(public readonly ShortifyUrl $url)
    {
        parent::__construct('This shortened URL has expired');
    }
}
