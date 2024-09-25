<?php

namespace MacbookAndrew\LaravelQueueCancelBatch\Commands;

use Illuminate\Bus\Batch;
use Illuminate\Bus\BatchRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

use function Laravel\Prompts\multiselect;

class LaravelQueueCancelBatchCommand extends Command
{
    public $signature = 'queue:cancel-batch {batchUuids?*}';

    public $description = 'Cancel one or more queue batches';

    /** @var Collection<array-key, string> */
    private Collection $batches;

    public function handle(): int
    {
        /** @var BatchRepository */
        $batchRepository = app()->make(BatchRepository::class);

        if ($this->argument('batchUuids')) {
            $this->batches = collect($this->argument('batchUuids'));
        } else {
            $allBatches = collect($batchRepository->get(100, null))->reject(fn (Batch $batch) => $batch->pendingJobs < 1 || $batch->finished() || $batch->canceled());

            if ($allBatches->isEmpty()) {
                $this->info('There are no active batches.');

                return self::INVALID;
            }

            /** @var array<int, string> */
            $batchIds = multiselect(
                label: 'Select one or more batches to cancel:',
                options: $allBatches->mapWithKeys(fn (Batch $batch) => [$batch->id => sprintf(
                    '%s (%d/%d completed jobs; started %s)',
                    $batch->name,
                    $batch->pendingJobs,
                    $batch->totalJobs,
                    $batch->createdAt->diffForHumans(),
                )])->all()
            );

            $this->batches = collect($batchIds);
        }

        if ($this->batches->isEmpty()) {
            $this->info('There are no active batches.');

            return self::INVALID;
        }

        $this->batches->each(function (string $uuid) {
            $batch = Bus::findBatch($uuid);

            if (! $batch) {
                $this->error('Could not find batch '.$uuid);

                return;
            }

            $batch->cancel();

            $this->info('Cancelled batch '.$batch->name);
        });

        return self::SUCCESS;
    }
}
