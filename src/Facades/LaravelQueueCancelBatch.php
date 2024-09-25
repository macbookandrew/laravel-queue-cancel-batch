<?php

namespace MacbookAndrew\LaravelQueueCancelBatch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MacbookAndrew\LaravelQueueCancelBatch\LaravelQueueCancelBatch
 */
class LaravelQueueCancelBatch extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MacbookAndrew\LaravelQueueCancelBatch\LaravelQueueCancelBatch::class;
    }
}
