<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\ParallelTesting;
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
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('totp.models.user', User::class);
        $app['config']->set('shortify.models.user', User::class);

        $app->useStoragePath(realpath(__DIR__.'/../workbench/storage'));
        $app['config']->set('shortify.channel.path', storage_path('logs/audit-'.ParallelTesting::token().'.log'));

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
        File::delete(config('shortify.channel.path'));

        parent::tearDown();
    }
}
