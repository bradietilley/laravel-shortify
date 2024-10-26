<?php

use BradieTilley\Shortify\Models\ShortifyUrl;

test('Short Url endpoint returns redirect to original url', function () {
    $url = ShortifyUrl::factory()->code('34534534')->createOne();

    $this->get(route('shortify.url', [
        'code' => '34534534',
    ]))->assertRedirect($url->original_url);
});
