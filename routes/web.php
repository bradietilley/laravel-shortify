<?php

use BradieTilley\Shortify\Http\Controllers\ShortUrlController;
use BradieTilley\Shortify\ShortifyConfig;
use Illuminate\Support\Facades\Route;

if (ShortifyConfig::getRoutingRoute() === null) {
    Route::group([
        'domain' => ShortifyConfig::getRoutingDomain(),
    ], function () {
        Route::get(ShortifyConfig::getRoutingUri(), ShortUrlController::class)->name('shortify.url');
    });
}
