<?php

use App\Models\User;
use BradieTilley\Shortify\Models\ShortifyUrl;
use BradieTilley\Shortify\Models\ShortifyVisit;

return [
    'models' => [
        /**
         * The model that represents a user in the application.
         *
         * @var class-string<\Illuminate\Foundation\Auth\User>
         */
        'user' => User::class,

        /**
         * The model that represents a single shortened URL.
         *
         * @var class-string<\BradieTilley\Shortify\Models\ShortifyUrl>
         */
        'shortify_url' => ShortifyUrl::class,

        /**
         * The model that represents a user's/guest's viewing of a shortened URL.
         *
         * @var class-string<\BradieTilley\Shortify\Models\ShortifyVisit>
         */
        'shortify_visit' => ShortifyVisit::class,
    ],

    'routing' => [
        /**
         * The relative path to the domain to use for routing all short URLs. The
         * only route param supported here is `{code}`. If omitted, the code will
         * be appended as a query param.
         *
         * @var string
         */
        'uri' => '/s/{code}',

        /**
         * The domain to use for all shortened URLs, such as if you want
         * to route all short URLs via a shorter domain alias.
         *
         * @var string|null
         */
        'domain' => null,

        /**
         * Alternative control to `uri` + `domain`, you may wish to entirely swap
         * out the route for a custom one.
         *
         * If left null, the default route is `shortify.url` which will be handled
         * internally by this package.
         *
         * If a string is provided, the default route (`shortify.url`) will not be
         * registered (that route will not serve as an entrypoint to the redirect
         * logic) where the expectation is that you will manage the redirect logic
         * yourself within that route.
         *
         * @var string|null
         */
        'route' => null,

        /**
         * The length of which to generate random codes that are used in the short
         * URLs.
         *
         * @var int
         */
        'code_length' => 12,
    ],

    'database' => [
        /**
         * The collation to use for the `code` field to enforce case sensitivity.
         */
        'code_field_collation' => 'ascii_bin',
    ],

    'feature' => [
        /**
         * Whether or not to track visits automatically
         *
         * @var bool
         */
        'track_visits' => true,
    ],
];
