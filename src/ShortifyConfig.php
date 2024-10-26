<?php

namespace BradieTilley\Shortify;

use BradieTilley\Shortify\Models\ShortifyUrl;
use BradieTilley\Shortify\Models\ShortifyVisit;
use Illuminate\Foundation\Auth\User;

class ShortifyConfig
{
    /** @var array<string, mixed> */
    protected static array $cache = [];

    protected static function get(string $key, mixed $default = null): mixed
    {
        return static::$cache[$key] ??= config("shortify.{$key}", $default);
    }

    public static function clearCache(): void
    {
        static::$cache = [];
    }

    /**
     * The model that represents a user in the application.
     *
     * @return class-string<User>
     */
    public static function getUserModel(): string
    {
        /** @phpstan-ignore-next-line */
        return static::get('models.user', User::class);
    }

    /**
     * The model that represents a single shortened URL.
     *
     * @return class-string<ShortifyUrl>
     */
    public static function getShortUrlModel(): string
    {
        /** @phpstan-ignore-next-line */
        return static::get('models.shortify_url', ShortifyUrl::class);
    }

    /**
     * The model that represents a user's/guest's viewing of a shortened URL.
     *
     * @return class-string<ShortifyVisit>
     */
    public static function getShortUrlVisitModel(): string
    {
        /** @phpstan-ignore-next-line */
        return static::get('models.shortify_visit', ShortifyVisit::class);
    }

    /**
     * The relative path to the domain to use for routing all short URLs. The
     * only route param supported here is `{code}`. If omitted, the code will
     * be appended as a query param.
     */
    public static function getRoutingUri(): string
    {
        /** @phpstan-ignore-next-line */
        return static::get('routing.uri');
    }

    /**
     * The domain to use for all shortened URLs, such as if you want
     * to route all short URLs via a shorter domain alias.
     */
    public static function getRoutingDomain(): ?string
    {
        /** @phpstan-ignore-next-line */
        return static::get('routing.domain');
    }

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
     */
    public static function getRoutingRoute(): ?string
    {
        /** @phpstan-ignore-next-line */
        return static::get('routing.route');
    }

    /**
     * Get the length of which to generate random codes that are used in the
     * short URLs.
     */
    public static function getCodeLength(): int
    {
        return static::get('routing.code_length');
    }
}
