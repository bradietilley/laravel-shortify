<?php

use BradieTilley\Shortify\Models\ShortifyUrl;
use BradieTilley\Shortify\Models\ShortifyVisit;
use Workbench\App\Models\User;

test('Short Url endpoint returns redirect to original url', function () {
    $url = ShortifyUrl::factory()->code('34534534')->createOne();

    $this->get(route('shortify.url', [
        'code' => '34534534',
    ]))->assertRedirect($url->original_url);
});

test('Short Url endpoint tracks visits', function (bool $authenticated) {
    $url = ShortifyUrl::factory()->code('34534534')->createOne();
    $user = null;

    if ($authenticated) {
        $user = User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '',
        ]);

        $this->actingAs($user);
    }

    expect($url->refresh()->visits->count())->toBe(0);

    $this->get(route('shortify.url', [
        'code' => '34534534',
    ]))->assertRedirect($url->original_url);
    expect($url->refresh()->visits->count())->toBe(1);

    $this->get(route('shortify.url', [
        'code' => '34534534',
    ]))->assertRedirect($url->original_url);
    expect($url->refresh()->visits->count())->toBe(2);

    $visit = ShortifyVisit::latest('id')->first();

    /** Reverse relation works */
    expect($visit->url->is($url))->toBe(true);

    /** User relation works */
    expect($visit->user?->id)->toBe($user?->id);
})->with([
    'authenticated' => true,
    'unauthenticated' => false,
]);
