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
     * @return class-string<User>
     */
    public static function getUserModel(): string
    {
        /** @phpstan-ignore-next-line */
        return static::get('models.user', User::class);
    }

    /**
     * @return class-string<ShortifyUrl>
     */
    public static function getShortUrlModel(): string
    {
        /** @phpstan-ignore-next-line */
        return static::get('models.shortify_url', ShortifyUrl::class);
    }

    /**
     * @return class-string<ShortifyVisit>
     */
    public static function getShortUrlVisitModel(): string
    {
        /** @phpstan-ignore-next-line */
        return static::get('models.shortify_visit', ShortifyVisit::class);
    }

    public static function getLogChannel(): ?string
    {
        /** @phpstan-ignore-next-line */
        return static::get('log_channel');
    }

    public static function getUri(): string
    {
        /** @phpstan-ignore-next-line */
        return static::get('routing.uri');
    }

    public static function getCodeLength(): int
    {
        return static::get('routing.code_length');
    }
}
