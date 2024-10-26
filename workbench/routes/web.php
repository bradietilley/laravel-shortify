<?php

use BradieTilley\Shortify\Shortify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('example-page/{int}', function (Request $request, int $int) {
    Shortify::log('Something happened', [
        'int' => $int,
    ]);

    return response()->json([
        'int' => $int,
        'name' => $request->user()?->name,
    ]);
});
