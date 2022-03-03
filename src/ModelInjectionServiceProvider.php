<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Soyhuce\ModelInjection\Commands\ModelInjectionCommand;

class ModelInjectionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-model-injection')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-model-injection_table')
            ->hasCommand(ModelInjectionCommand::class);
    }
}
