# Laravel Shortify

A simple yet flexible implementation of a URL Shortner in Laravel.

![Static Analysis](https://github.com/bradietilley/laravel-shortify/actions/workflows/static.yml/badge.svg)
![Tests](https://github.com/bradietilley/laravel-shortify/actions/workflows/tests.yml/badge.svg)
![Laravel Version](https://img.shields.io/badge/Laravel%20Version-%E2%89%A5%2011.0-F9322C)
![PHP Version](https://img.shields.io/badge/PHP%20Version-%E2%89%A5%208.3-4F5B93)


## Introduction

Integrate your own cusom URL shortener into your Laravel App, and track *who* visits *what*!

## Installation

```
composer require bradietilley/laravel-shortify
```

Config and migrations should be published:

```
artisan vendor:publish --tag="shortify-config"
artisan vendor:publish --tag="shortify-migrations"
```


## Documentation

### Shortening URLs

**Statically:**

You can statically call the `BradieTilley\Shortify\Shortify` singleton to shorten urls:

```php
$shortUrl = Shortify::make()->shorten($longUrl)->url; // https://app.com/s/Ws4BYCVLDDDh
$shortUrl = Shortify::url($longUrl)->url; //             https://app.com/s/5wHiKrKx5xV1
```

**Dependency Injection:**

The `BradieTilley\Shortify\Shortify` singleton can be dependency injected:

```php
public function handle(Shortify $shortify): void
{
    $url = $shortify->shorten($this->invoice->getSignedUrl());

    echo $url->url; // https://app.com/s/xpLurjDkBATw
}
```

### URLs → `ShortifyUrl` Model

The `BradieTilley\Shortify\Models\ShortifyUrl` model is the map between an original URL and a short URL (unique by code).

**Original URL**

The original URL can be fetched via the `original_url` property

```php
$url = Shortify::url($longUrl);
$url->original_url; // same as $longUrl
```

**Shortened URL**

The shortened URL can be fetched via the `url` attribute

```php
$url = Shortify::url($longUrl);
$url->url; // https://app.com/s/xpLurjDkBATw
```

The `ShortifyUrl` model overview:

- Fields:
 - `id` → auto incrementing ID
 - `code` → Unique URL slug/code
 - `original_url` → The original URL (text length of 64 KB)
 - `visit_count` → A running count of how many times this URL has been visited, for optimised querying (albeit, at the cost of continually updating this field)
 - `expired` → Whether or not this URL has expired
 - `expires_at` → Timestamp of when this URL expires
 - `created_at` → Timestamp of when this URL was created
 - `updated_at` → Timestamp of when this URL was updated (which will typically correspond to when it was last visited)
- Attributes:
  - `url` → A computed shortened URL using the code and the current route configuration.
- Relations:
  - `visits` → Has many `ShortifyVisit` models.

### Visits ->`ShortifyVisit` Model

The `BradieTilley\Shortify\Models\ShortifyVisit` model represents a unique visit of a shortened url, tracking the user (if authenticated), IP address, and User Agent.

```php
$url = ShortifyUrl::findByCode('my-short-url');

$url->visits; // Collection<ShortifyVisit>
```

The `ShortifyVisit` model overview:

- Fields:
  - `id` → auto incrementing ID
  - `shortify_url_id` → Foreign Key for `ShortifyUrl`
  - `user_id` → Foreign Key for the visited `User`
  - `ip` → IP Address of visitor
  - `user_agent` → User Agent of visitor
  - `created_at` → Timestamp of when the user visited the URL
- Relations:
  - `user` → The `User` who visited the URL
  - `url` → The `ShortifyUrl` visited

### Customisation → Code Length

The code length defaults to 12, meaning the short URLs are always `https://app.com/s/` followed by 12 alpha-numeric characters such as `50mqV1dfOrth`.

This can be configured via the `shortify.routing.code_length` config variable.

### Customisation → URL Path

The default is `/s/{code}` but in some cases this won't be what you're after.

This can be configured via the `shortify.routing.uri` config variable.

### Customisation → URL Domain

The default is the default app domain but in some cases you may wish to use an alternative shorthand domain.

Note: You will need to register this other domain and configure the DNS as per usual -- this package does NOT provide *that* type of functionality.

This can be configured via the `shortify.routing.domain` config variable.

### Customisation → Redirect Controller

The default is a rudimentary redirect that is handled by this package, but sometimes you may want to customise how the redirect is handled -- perhaps you want it to be returned in a JSON response that matches how JSON redirects are performed in your app.

This can be configured via the `shortify.routing.route` config variable.

Once configured, the original controller is inaccessible, and you now have full control over the handling of redirects.

### Customisation → Models

As always with most Laravel packages, you can modify the models to use -- perhaps you want to track more information, add new helpers, etc. No worries.

This can be configured via the `shortify.models.shortify_url` and `shortify.models.shortify_visit` config variables which change the `ShortifyUrl` and `ShortifyVisit` models respectively.

### Customisation → Everything else

The `BradieTilley\Shortify\Shortify` singleton can be replaced by another Shortify instance within your service provider. For example:

Extend Shortify:

```php
namespace App\Support;

class Shortify extends \BradieTilley\Shortify\Shortify
{
    public function generateCode(string $url): string
    {
        return Carbon::now()->format('Ymd').'-'.Str::random(6);
    }
}
```

Then register it:

```php
$this->app->bind(\BradieTilley\Shortify\Shortify::class, \App\Support\Shortify::class);
```

Then use it:

```php
echo Shortify::url($longUrl)->url; // 20241026-TWrCmX
```

## Author

- [Bradie Tilley](https://github.com/bradietilley)
