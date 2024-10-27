<?php

use BradieTilley\Shortify\Models\ShortifyUrl;
use Illuminate\Support\Facades\Config;

test('ShortUrl can lookup a shortened url by its code', function () {
    $url1 = ShortifyUrl::factory()->code('0000001')->createOne();
    $url2 = ShortifyUrl::factory()->code('0000002')->createOne();
    $url3 = ShortifyUrl::factory()->code('0000003')->createOne();

    $url4 = ShortifyUrl::findByCode('0000002');

    expect($url2->is($url4))->toBe(true);
});

test('ShortUrl can lookup a shortened url by its code in a case sensitive manner', function () {
    $url1 = ShortifyUrl::factory()->code('or9gah74')->createOne();
    $url2 = ShortifyUrl::factory()->code('fSiqaL6O')->createOne();
    $url3 = ShortifyUrl::factory()->code('wrcvR9Ba')->createOne();

    $url4 = ShortifyUrl::findByCode('wrcvR9BA');
    expect($url4)->toBeNull();

    $url4 = ShortifyUrl::findByCode('WrcvR9Ba');
    expect($url4)->toBeNull();

    $url4 = ShortifyUrl::findByCode('wrcvR9Ba');
    expect($url4)->toBeInstanceOf(ShortifyUrl::class)->is($url3)->toBeTrue();
});

test('ShortUrl can lookup a shortened url by its code in a case sensitive manner (mysql)', function () {
    $url1 = ShortifyUrl::factory()->code('or9gah74')->createOne();
    $url2 = ShortifyUrl::factory()->code('fSiqaL6O')->createOne();
    $url3 = ShortifyUrl::factory()->code('wrcvR9Ba')->createOne();

    // Mock a MySQL connection by setting the driver name to 'mysql'
    Config::set('database.connections.mysql', [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'test_database',
        'username' => 'test_user',
        'password' => 'test_password',
    ]);

    // Set the default connection to the mocked MySQL connection
    Config::set('database.default', 'mysql');

    $query = ShortifyUrl::query()->byCode('wrcvR9Ba');
    $sql = str_replace('?', implode('', $query->getBindings()), $query->toSql());
    expect($sql)->toBe('select * from `shortify_urls` where BINARY code = wrcvR9Ba');
});
