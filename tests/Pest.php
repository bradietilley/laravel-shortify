<?php

use BradieTilley\Shortify\Models\ShortifyUrl;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\ExpectationFailedException;

uses(Tests\TestCase::class)->in('Feature', 'Unit');

if (! function_exists('test_app_path')) {
    function test_app_path(string $relative = ''): string
    {
        return __DIR__.'/Fixtures/'.ltrim($relative, '/');
    }
}

expect()->extend('toMatchSql', function (string $expect) {
    $clean = function (string|object $sql): string {
        $sql = is_string($sql) ? $sql : $sql->toRawSql();

        $sql = Str::of($sql)
            ->replace(['`', '"', "'"], '')
            ->replaceMatches("/\s+/", ' ')
            ->replaceMatches('/\s*([\(\)])\s*/', '$1')
            ->replace('\\\\', '\\')
            ->lower()
            ->trim()
            ->toString();

        return $sql;
    };

    $actual = $clean($this->value);
    $expect = $clean($expect);

    $this->value = $actual;

    return $this->toBe($expect);
});

expect()->extend('auditLogDatabaseHas', function ($assertions) {
    foreach (Arr::wrap($assertions) as $assertion) {
        expect(ShortifyUrl::query()->where('event', $assertion)->exists())->toBeTrue();
    }

    return true;
});

expect()->extend('auditLogDatabaseMissing', function ($assertions) {
    foreach (Arr::wrap($assertions) as $assertion) {
        expect(ShortifyUrl::query()->where('event', $assertion)->exists())->toBeFalse();
    }

    return true;
});

expect()->extend('auditLogChannelHas', function ($assertions) {
    $logger = new StreamHandler(config('shortify.channel.path'));
    $test = test();

    $test->assertFileExists($logger->getUrl());
    $content = file_get_contents($logger->getUrl());

    foreach (Arr::wrap($assertions) as $assertion) {
        $test->assertStringContainsString($assertion, $content);
    }

    return true;
});

expect()->extend('auditLogChannelMissing', function ($assertions) {
    $logger = new StreamHandler(config('shortify.channel.path'));
    $test = test();

    try {
        $test->assertFileDoesNotExist($logger->getUrl());

        return true;
    } catch (ExpectationFailedException) {
        // file exists, but lets see if the log itself exists...
    }
    $content = file_get_contents($logger->getUrl());

    foreach (Arr::wrap($assertions) as $assertion) {
        $test->assertStringNotContainsString($assertion, $content);
    }

    return true;
});
