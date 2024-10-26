<?php

namespace BradieTilley\Shortify;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShortifyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('shortify')
            ->hasMigrations('create_shortify_urls_table', 'create_shortify_visits_table')
            ->hasConfigFile('shortify')
            ->hasRoute('web');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ShortifyConfig::class, ShortifyConfig::class);
        $this->app->singleton(Shortify::class, Shortify::class);
    }
}
