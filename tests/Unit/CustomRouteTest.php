<?php

use BradieTilley\Shortify\Models\ShortifyUrl;
use BradieTilley\Shortify\Shortify;

test('can shorten urls under a specific route', function () {
    // custom-route-test
    $longUrl = 'https://localhost/some/page/to/something/far-too-long-for-sms-as-that-will-cost-more-credits';

    $short = Shortify::url($longUrl);

    expect($short)->toBeInstanceOf(ShortifyUrl::class);

    // See TestCase for domain name registration
    expect($short->url)->toBe('http://localhost/custom-route-test/'.$short->code);

    $this->getJson($short->url)
        ->assertOk()
        ->assertJson([
            'redirect' => $longUrl,
        ]);
});
