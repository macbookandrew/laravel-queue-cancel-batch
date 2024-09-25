<?php

use Illuminate\Bus\Batch;
use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use MacbookAndrew\LaravelQueueCancelBatch\Commands\LaravelQueueCancelBatchCommand;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

it('can cancel a batch given a UUID', function () {
    /** @var \Illuminate\Bus\BatchRepository */
    $mockBatchRepository = mock(DatabaseBatchRepository::class);

    $testBatch = new Batch(
        queue: app()->make(QueueFactory::class),
        repository: $mockBatchRepository,
        id: Str::uuid()->toString(),
        name: 'Test Batch',
        totalJobs: 100,
        pendingJobs: 50,
        failedJobs: 0,
        failedJobIds: [],
        options: [],
        createdAt: now()->subMinute()->toImmutable(),
    );
    $testBatch2 = new Batch(
        queue: app()->make(QueueFactory::class),
        repository: $mockBatchRepository,
        id: Str::uuid()->toString(),
        name: 'Test Batch 2',
        totalJobs: 100,
        pendingJobs: 50,
        failedJobs: 0,
        failedJobIds: [],
        options: [],
        createdAt: now()->subMinute()->toImmutable(),
    );

    Bus::shouldReceive('get')->with(100, null)->andReturn([$testBatch, $testBatch2]);
    Bus::shouldReceive('findBatch')->with($testBatch->id)->andReturn($testBatch);

    /** @var \Mockery\MockInterface $mockBatchRepository */
    $mockBatchRepository->shouldReceive('cancel')->with($testBatch->id);

    artisan(LaravelQueueCancelBatchCommand::class, [
        'batchUuids' => [$testBatch->id],
    ])->assertSuccessful();
});

it('can ask for existing batches', function () {
    /** @var \Illuminate\Bus\BatchRepository */
    $mockBatchRepository = mock(DatabaseBatchRepository::class);

    $testBatch1 = new Batch(
        queue: app()->make(QueueFactory::class),
        repository: $mockBatchRepository,
        id: Str::uuid()->toString(),
        name: 'Test Batch',
        totalJobs: 100,
        pendingJobs: 50,
        failedJobs: 0,
        failedJobIds: [],
        options: [],
        createdAt: now()->subMinute()->toImmutable(),
    );
    $testBatch2 = new Batch(
        queue: app()->make(QueueFactory::class),
        repository: $mockBatchRepository,
        id: Str::uuid()->toString(),
        name: 'Test Batch 2',
        totalJobs: 100,
        pendingJobs: 33,
        failedJobs: 0,
        failedJobIds: [],
        options: [],
        createdAt: now()->subMinute()->toImmutable(),
    );
    $testBatch3 = new Batch(
        queue: app()->make(QueueFactory::class),
        repository: $mockBatchRepository,
        id: Str::uuid()->toString(),
        name: 'Test Batch 2',
        totalJobs: 100,
        pendingJobs: 33,
        failedJobs: 0,
        failedJobIds: [],
        options: [],
        createdAt: now()->subMinute()->toImmutable(),
    );

    /** @var \Mockery\MockInterface $mockBatchRepository */
    $mockBatchRepository->shouldReceive('get')->with(100, null)->andReturn([$testBatch1, $testBatch2, $testBatch3]);
    Bus::shouldReceive('findBatch')->with($testBatch2->id)->andReturn($testBatch2);
    Bus::shouldReceive('findBatch')->with($testBatch3->id)->andReturn($testBatch3);

    $mockBatchRepository->shouldReceive('cancel')->with($testBatch2->id);
    $mockBatchRepository->shouldReceive('cancel')->with($testBatch3->id);

    artisan(LaravelQueueCancelBatchCommand::class)
        ->expectsQuestion(
            question: 'Select one or more batches to cancel',
            answer: [$testBatch2->id, $testBatch3->id],
        )
        ->assertSuccessful();
});

it('displays error message when there are no batches', function () {
    mock(DatabaseBatchRepository::class)->shouldReceive('get')->with(100, null)->andReturn([]);

    artisan(LaravelQueueCancelBatchCommand::class)
        ->expectsOutput('There are no active batches.')
        ->assertFailed();
});
