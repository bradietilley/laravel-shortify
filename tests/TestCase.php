<?php

namespace Tests;

use BradieTilley\Shortify\ShortifyConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Workbench\App\Models\User;

abstract class TestCase extends TestbenchTestCase
{
    use WithWorkbench;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ShortifyConfig::clearCache();
    }

    public function getEnvironmentSetUp($app)
    {
        if (str_ends_with($this::class, 'CustomDomainTest')) {
            $app['config']->set('shortify.routing.domain', 'http://example.org');
        }

        if (str_ends_with($this::class, 'CustomRouteTest')) {
            $app['config']->set('shortify.routing.route', 'custom-route-test');
        }

        $app->useStoragePath(realpath(__DIR__.'/../workbench/storage'));

        $app['config']->set('shortify.models.user', User::class);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', Str::random(32));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
