<?php

namespace MacbookAndrew\LaravelQueueCancelBatch\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use MacbookAndrew\LaravelQueueCancelBatch\LaravelQueueCancelBatchServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MacbookAndrew\\LaravelQueueCancelBatch\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelQueueCancelBatchServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-queue-cancel-batch_table.php.stub';
        $migration->up();
        */
    }
}
