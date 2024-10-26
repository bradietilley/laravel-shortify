<?php

namespace BradieTilley\Shortify\Exceptions;

class ShortifyUrlCodeAlreadyExistsException extends ShortifyException
{
    public function __construct(public readonly string $code)
    {
        parent::__construct(sprintf('A shortify URL already exists with the code `%s`', $code));
    }
}
