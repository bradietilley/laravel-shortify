<?php

namespace BradieTilley\Shortify\Exceptions;

class ShortifyUrlCodeAlreadyExistsException extends ShortifyException
{
    public function __construct(public readonly string $urlCode)
    {
        parent::__construct(sprintf('A shortify URL already exists with the code `%s`', $urlCode));
    }
}
