<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\TaskListCacheContract;
use App\Contracts\TaskRepositoryContract;
use App\Data\CreateTask;
use App\Data\ListTasksQuery;
use App\Data\UpdateTask;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\CachingTaskRepository;
use Carbon\CarbonImmutable;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

final class CachingTaskRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_paginate_delegates_to_cache_remember(): void
    {
        $listQuery = new ListTasksQuery(null, null, 1, 10);
        $paginator = new LengthAwarePaginator([], 0, 10, 1);

        $inner = Mockery::mock(TaskRepositoryContract::class);
        $inner->shouldReceive('paginate')->never();

        $cache = Mockery::mock(TaskListCacheContract::class);
        $cache->shouldReceive('remember')
            ->once()
            ->with($listQuery, Mockery::type('Closure'))
            ->andReturn($paginator);

        $repository = new CachingTaskRepository($inner, $cache);

        $this->assertSame($paginator, $repository->paginate($listQuery));
    }

    public function test_find_by_id_delegates_to_cache_remember_show(): void
    {
        $task = new Task;
        $task->id = 5;

        $inner = Mockery::mock(TaskRepositoryContract::class);
        $inner->shouldReceive('findById')->never();

        $cache = Mockery::mock(TaskListCacheContract::class);
        $cache->shouldReceive('rememberShow')
            ->once()
            ->with(5, Mockery::type('Closure'))
            ->andReturn($task);

        $repository = new CachingTaskRepository($inner, $cache);

        $this->assertSame($task, $repository->findById(5));
    }

    public function test_create_flushes_list_cache(): void
    {
        $taskData = new CreateTask(
            title: 'Задача',
            description: null,
            dueDate: CarbonImmutable::parse('2025-01-20T15:00:00'),
            createDate: CarbonImmutable::parse('2025-01-20T15:00:00'),
            status: TaskStatus::Pending,
            priority: TaskPriority::High,
            category: 'Работа',
        );

        $task = new Task;
        $task->id = 1;

        $inner = Mockery::mock(TaskRepositoryContract::class);
        $inner->shouldReceive('create')->once()->with($taskData)->andReturn($task);

        $cache = Mockery::mock(TaskListCacheContract::class);
        $cache->shouldReceive('flush')->once();

        $repository = new CachingTaskRepository($inner, $cache);

        $this->assertSame($task, $repository->create($taskData));
    }

    public function test_update_flushes_list_cache(): void
    {
        $task = new Task;
        $task->id = 2;

        $taskData = new UpdateTask(
            title: 'Обновлено',
            description: null,
            dueDate: CarbonImmutable::parse('2025-01-25T18:00:00'),
            status: TaskStatus::Completed,
            priority: TaskPriority::Low,
            category: 'Дом',
        );

        $updated = new Task;
        $updated->id = 2;

        $inner = Mockery::mock(TaskRepositoryContract::class);
        $inner->shouldReceive('update')->once()->with($task, $taskData)->andReturn($updated);

        $cache = Mockery::mock(TaskListCacheContract::class);
        $cache->shouldReceive('flush')->once();
        $cache->shouldReceive('forgetShow')->once()->with(2);

        $repository = new CachingTaskRepository($inner, $cache);

        $this->assertSame($updated, $repository->update($task, $taskData));
    }

    public function test_delete_flushes_list_cache(): void
    {
        $task = new Task;
        $task->id = 3;

        $inner = Mockery::mock(TaskRepositoryContract::class);
        $inner->shouldReceive('delete')->once()->with($task);

        $cache = Mockery::mock(TaskListCacheContract::class);
        $cache->shouldReceive('flush')->once();
        $cache->shouldReceive('forgetShow')->once()->with(3);

        $repository = new CachingTaskRepository($inner, $cache);

        $repository->delete($task);

        $this->addToAssertionCount(1);
    }
}
