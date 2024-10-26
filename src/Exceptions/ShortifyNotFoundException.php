<?php

namespace BradieTilley\Shortify\Exceptions;

class ShortifyNotFoundException extends ShortifyException
{
    public function __construct(public readonly string $url)
    {
        parent::__construct('This shortened URL could not be found');
    }
}
