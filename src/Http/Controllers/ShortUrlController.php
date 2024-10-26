<?php

namespace BradieTilley\Shortify\Http\Controllers;

use BradieTilley\Shortify\ShortifyConfig;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ShortUrlController
{
    /**
     * View Short URL
     */
    public function __invoke(string $code): RedirectResponse
    {
        $model = ShortifyConfig::getShortUrlModel();

        return $model::redirectTo($code);
    }
}
