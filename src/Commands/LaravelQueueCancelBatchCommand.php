<?php

namespace MacbookAndrew\LaravelQueueCancelBatch\Commands;

use Illuminate\Console\Command;

class LaravelQueueCancelBatchCommand extends Command
{
    public $signature = 'laravel-queue-cancel-batch';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
