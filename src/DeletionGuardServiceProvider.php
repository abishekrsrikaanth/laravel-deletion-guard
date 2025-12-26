<?php

namespace WorkDoneRight\DeletionGuard;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WorkDoneRight\DeletionGuard\Commands\AuditDeletionDependencies;

class DeletionGuardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-deletion-guard')
            ->hasConfigFile()
            ->hasCommand(AuditDeletionDependencies::class);
    }
}
