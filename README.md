# Laravel Shortify

A simple yet flexible implementation of a URL Shortner in Laravel.

![Static Analysis](https://github.com/bradietilley/laravel-shortify/actions/workflows/static.yml/badge.svg)
![Tests](https://github.com/bradietilley/laravel-shortify/actions/workflows/tests.yml/badge.svg)
![Laravel Version](https://img.shields.io/badge/Laravel%20Version-%3E=%2011.0-F9322C)
![PHP Version](https://img.shields.io/badge/PHP%20Version-%3E=%208.3-4F5B93)


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

Using the `Shortify` singleton you can shorten urls:

```php
$shortUrl = Shortify::make()->shorten($longUrl)->url;
```

## Author

- [Bradie Tilley](https://github.com/bradietilley)
