<?php

use App\Models\User;
use BradieTilley\Shortify\Models\ShortifyUrl;
use BradieTilley\Shortify\Models\ShortifyVisit;

return [
    'models' => [
        'user' => User::class,
        'shortify_url' => ShortifyUrl::class,
        'shortify_visit' => ShortifyVisit::class,
    ],

    'log_channel' => null,

    'routing' => [
        'uri' => '/s/{code}',

        'code_length' => 12,
    ],
];
