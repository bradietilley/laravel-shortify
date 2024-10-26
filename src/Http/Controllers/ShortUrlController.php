<?php

namespace BradieTilley\Shortify\Http\Controllers;

use BradieTilley\Shortify\Models\ShortifyUrl;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ShortUrlController
{
    /**
     * View Short URL
     */
    public function __invoke(string $code): RedirectResponse
    {
        return ShortifyUrl::redirectTo($code);
    }
}
