<?php

namespace BradieTilley\Shortify;

use BradieTilley\Shortify\Exceptions\ShortifyException;
use BradieTilley\Shortify\Models\ShortifyUrl;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class Shortify
{
    protected LoggerInterface $logger;

    public function __construct(
        protected ShortifyConfig $config,
        protected Request $request,
        protected Redirector $redirector,
        protected UrlGenerator $url,
    ) {
    }

    /**
     * Get the singleton instance
     */
    public static function make(): Shortify
    {
        /** @var Shortify $instance */
        $instance = app(Shortify::class);

        return $instance;
    }

    /**
     * Shorten the given URL.
     *
     * Static shortcut alias for `->shorten()`
     */
    public static function url(string $url, ?string $code = null): ShortifyUrl
    {
        return static::make()->shorten($url, $code);
    }

    /**
     * Generate a unique code for the given URL.
     */
    public function generateCode(string $url): string
    {
        return Str::random(ShortifyConfig::getCodeLength());
    }

    /**
     * Shorten the given URL.
     *
     * If a code is provided, it must be unique.
     * If no code is proivided, a unique code will be generated.
     *
     * @throws ShortifyUrlCodeAlreadyExistsException if code is provided and code is non-unique
     */
    public function shorten(string $url, ?string $code = null): ShortifyUrl
    {
        $model = ShortifyConfig::getShortUrlModel();

        if ($code !== null) {
            if (static::checkIfCodeExists($code)) {
                throw ShortifyException::codeAlreadyExists($code);
            }
        }

        if ($code === null) {
            for ($i = 0; $i < 1000; $i++) {
                $temp = $this->generateCode($url);

                if (static::checkIfCodeExists($temp) === false) {
                    $code = $temp;
                }
            }
        }

        $url = new $model([
            'ulid' => (string) Str::ulid(),
            'code' => $code,
            'original_url' => $url,
        ]);
        $url->save();

        return $url;
    }

    /**
     * Check if the given code exists, used to prevent duplicate codes.
     */
    public static function checkIfCodeExists(string $code): bool
    {
        $model = ShortifyConfig::getShortUrlModel();

        return $model::query()->where('code', $code)->exists();
    }

    /**
     * Get a redirect response to the original URL of the given short URL.
     */
    public function redirectToOriginalUrl(ShortifyUrl $url): RedirectResponse
    {
        return $this->redirector->away($url->original_url);
    }

    /**
     * Get the full shortened URL.
     */
    public function getShortenedUrl(ShortifyUrl $url): string
    {
        return $this->url->route(static::getRouteName(), [
            'code' => $url->code,
        ]);
    }

    /**
     * Get the name of the route that will handle the redirecting logic.
     */
    public static function getRouteName(): string
    {
        return ShortifyConfig::getRoutingRoute() ?? 'shortify.url';
    }

    /**
     * Get the authenticated user
     */
    public function user(): ?User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return null;
        }

        return $user;
    }
}
