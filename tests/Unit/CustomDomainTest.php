<?php

use BradieTilley\Shortify\Models\ShortifyUrl;
use BradieTilley\Shortify\Shortify;

test('ShortUrl can be created under a specific domain', function () {
    $longUrl = 'https://localhost/some/page/to/something/far-too-long-for-sms-as-that-will-cost-more-credits';

    $short = Shortify::url($longUrl);

    expect($short)->toBeInstanceOf(ShortifyUrl::class);

    // See TestCase for domain name registration
    expect($short->url)->toBe('http://example.org/s/'.$short->code);
});
