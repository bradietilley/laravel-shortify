<?php

use BradieTilley\Shortify\Models\ShortifyUrl;
use Illuminate\Support\Facades\Route;

Route::get('custom-route-test/{code}', function (ShortifyUrl $code) {
    return response()->json([
        'redirect' => $code->visit()->original_url,
    ]);
})->name('custom-route-test');
