<?php

use BradieTilley\Shortify\Events\ExpiredUrlAttempted;
use BradieTilley\Shortify\Events\InvalidUrlAttempted;
use BradieTilley\Shortify\Events\UrlCreated;
use BradieTilley\Shortify\Events\UrlDeleted;
use BradieTilley\Shortify\Events\UrlVisited;
use BradieTilley\Shortify\Exceptions\ShortifyExpiredException;
use BradieTilley\Shortify\Exceptions\ShortifyNotFoundException;
use BradieTilley\Shortify\Exceptions\ShortifyUrlCodeAlreadyExistsException;
use BradieTilley\Shortify\Models\ShortifyUrl;
use BradieTilley\Shortify\Shortify;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Workbench\App\Models\User;

test('ShortUrl can be created for a long URL', function () {
    $longUrl = 'https://localhost/some/page/to/something/far-too-long-for-sms-as-that-will-cost-more-credits';

    $short = Shortify::url($longUrl);

    expect($short)->toBeInstanceOf(ShortifyUrl::class);

    expect($short->url)->toBe('http://localhost/s/'.$short->code);
});

test('can shorten urls with a custom code', function () {
    $longUrl = 'https://localhost/some/page/to/something/far-too-long-for-sms-as-that-will-cost-more-credits';

    $url = Shortify::url($longUrl, 'ABC123');
    expect($url->code)->toBe('ABC123');

    /** Throws an exception when duplicate is found */
    expect(fn () => Shortify::url($longUrl, 'ABC123'))
        ->toThrow(ShortifyUrlCodeAlreadyExistsException::class);
});

test('ShortUrl can redirect to the original URL', function (bool $authenticated) {
    Event::fake();
    $user = null;

    if ($authenticated) {
        $user = User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '',
        ]);

        $this->actingAs($user);
    }

    $url1 = ShortifyUrl::factory()->code('0000001')->original('https://example.org/random/a18df8bc-107e-5ece-9fbb-386fc25d49a2')->createOne();
    $url2 = ShortifyUrl::factory()->code('0000002')->original('https://example.org/random/fc21f21f-b822-5fd7-8824-e986e54a5d5f')->createOne();
    $url3 = ShortifyUrl::factory()->code('0000003')->original('https://example.org/random/bdf7409c-0612-592f-adf1-afbb5dd7b3f4')->createOne();

    $response = ShortifyUrl::redirectTo('0000002');

    Event::assertDispatched(fn (UrlVisited $event) => $event->url->is($url2) && (
        $authenticated ? $event->visit->user->is($user) : ($event->visit->user === null)
    ));

    expect($response)->toBeInstanceOf(RedirectResponse::class);
    expect($response->getTargetUrl())->toBe('https://example.org/random/fc21f21f-b822-5fd7-8824-e986e54a5d5f');
})->with([
    'authenticated' => true,
    'guest' => false,
]);

test('ShortUrl will fire events when url is invalid', function () {
    Event::fake();

    expect(fn () => ShortifyUrl::redirectTo('3987593847543'))->toThrow(ShortifyNotFoundException::class);

    Event::assertDispatched(InvalidUrlAttempted::class);
    Event::assertNotDispatched(ExpiredUrlAttempted::class);
    Event::assertNotDispatched(UrlVisited::class);
});

test('ShortUrl will fire events when url is expired', function () {
    Event::fake();
    $url = ShortifyUrl::factory()->code('2396236')->expires(now()->addSecond())->createOne();
    ShortifyUrl::redirectTo('2396236');
    Event::assertNotDispatched(InvalidUrlAttempted::class);
    Event::assertNotDispatched(ExpiredUrlAttempted::class);
    Event::assertDispatched(UrlVisited::class);
    expect($url->refresh()->expired)->toBeFalse();

    Event::fake();
    $this->travelTo(now()->addSeconds(2));
    expect(fn () => ShortifyUrl::redirectTo('2396236'))->toThrow(ShortifyExpiredException::class);
    Event::assertNotDispatched(InvalidUrlAttempted::class);
    Event::assertDispatched(ExpiredUrlAttempted::class);
    Event::assertNotDispatched(UrlVisited::class);
    expect($url->refresh()->expired)->toBeTrue();

    Event::fake();
    $this->travelTo(now()->addSeconds(2));
    expect(fn () => ShortifyUrl::redirectTo('2396236'))->toThrow(ShortifyExpiredException::class);
    Event::assertNotDispatched(InvalidUrlAttempted::class);
    Event::assertDispatched(ExpiredUrlAttempted::class);
    Event::assertNotDispatched(UrlVisited::class);
    expect($url->refresh()->expired)->toBeTrue();
});

test('ShortUrl will fire event when created', function () {
    Event::fake();

    $url = ShortifyUrl::factory()->code('2396236')->expires(now()->addSecond())->createOne();
    Event::assertDispatched(fn (UrlCreated $event) => $event->url->is($url));
});

test('ShortUrl will fire event when deleted', function () {
    $url = ShortifyUrl::factory()->code('2396236')->expires(now()->addSecond())->createOne();
    Event::fake();

    $url->delete();
    Event::assertDispatched(fn (UrlDeleted $event) => $event->url->is($url));
});
