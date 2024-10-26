<?php

use BradieTilley\Shortify\Http\Controllers\ShortUrlController;
use BradieTilley\Shortify\ShortifyConfig;
use Illuminate\Support\Facades\Route;

Route::get(ShortifyConfig::getUri(), ShortUrlController::class)->name('shortify.url');
