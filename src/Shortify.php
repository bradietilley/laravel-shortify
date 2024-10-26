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

    public static function make(): Shortify
    {
        /** @var Shortify $instance */
        $instance = app(Shortify::class);

        return $instance;
    }

    public static function url(string $url, ?string $code = null): ShortifyUrl
    {
        return static::make()->shorten($url, $code);
    }

    public function generateCode(string $url): string
    {
        return Str::random(ShortifyConfig::getCodeLength());
    }

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

        return $url;
    }

    public static function checkIfCodeExists(string $code): bool
    {
        $model = ShortifyConfig::getShortUrlModel();

        return $model::query()->where('code', $code)->exists();
    }

    public function getRedirectResponse(ShortifyUrl $url): RedirectResponse
    {
        return $this->redirector->away($url->original_url);
    }

    public function getShortUrl(ShortifyUrl $url): string
    {
        return $this->url->route('shortify.url', [
            'code' => $url->code,
        ]);
    }

    public function user(): ?User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return null;
        }

        return $user;
    }
}
