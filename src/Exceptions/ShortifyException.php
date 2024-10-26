<?php

namespace BradieTilley\Shortify\Exceptions;

use BradieTilley\Shortify\Models\ShortifyUrl;
use Exception;

abstract class ShortifyException extends Exception
{
    public static function codeAlreadyExists(string $code): ShortifyUrlCodeAlreadyExistsException
    {
        return new ShortifyUrlCodeAlreadyExistsException($code);
    }

    public static function notFound(string $url): ShortifyNotFoundException
    {
        return new ShortifyNotFoundException($url);
    }

    public static function expired(ShortifyUrl $url): ShortifyExpiredException
    {
        return new ShortifyExpiredException($url);
    }
}
