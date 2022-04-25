<?php

namespace Trov\Discoveries;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class TrovDiscoveriesServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('trov-discoveries')
            ->hasCommand(Commands\InstallTrovDiscoveries::class)
            ->hasMigrations([
                'create_discoveries_tables',
            ]);
    }
}
