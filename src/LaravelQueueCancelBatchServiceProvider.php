<?php

namespace MacbookAndrew\LaravelQueueCancelBatch;

use MacbookAndrew\LaravelQueueCancelBatch\Commands\LaravelQueueCancelBatchCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelQueueCancelBatchServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-queue-cancel-batch')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_queue_cancel_batch_table')
            ->hasCommand(LaravelQueueCancelBatchCommand::class);
    }
}
