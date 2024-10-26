# Laravel URL Shortener

## Introduction

Integrate your own cusom URL shortener into your Laravel App, and track who visits what!

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

Using the `Shortify` singleton you can shorten urls:

```php
$shortUrl = Shortify::make()->shorten($longUrl)->url;
```

## Author

- [Bradie Tilley](https://github.com/bradietilley)
