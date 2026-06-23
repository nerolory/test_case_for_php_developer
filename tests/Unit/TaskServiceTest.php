<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\TaskRepositoryContract;
use App\Data\CreateTask;
use App\Data\ListTasksQuery;
use App\Data\UpdateTask;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Exceptions\TaskNotFoundException;
use App\Models\Task;
use App\Services\TaskService;
use Carbon\CarbonImmutable;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

final class TaskServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_create_delegates_to_repository(): void
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

        $repository = Mockery::mock(TaskRepositoryContract::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($taskData)
            ->andReturn($task);

        $service = new TaskService($repository);

        $this->assertSame($task, $service->create($taskData));
    }

    public function test_get_by_id_throws_when_task_missing(): void
    {
        $repository = Mockery::mock(TaskRepositoryContract::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(99)
            ->andReturn(null);

        $service = new TaskService($repository);

        $this->expectException(TaskNotFoundException::class);

        $service->getById(99);
    }

    public function test_list_delegates_to_repository(): void
    {
        $listQuery = new ListTasksQuery(
            search: 'Задача',
            sort: 'due_date',
            page: 1,
            perPage: 10,
        );

        $paginator = new LengthAwarePaginator([], 0, 10, 1);

        $repository = Mockery::mock(TaskRepositoryContract::class);
        $repository->shouldReceive('paginate')
            ->once()
            ->with($listQuery)
            ->andReturn($paginator);

        $service = new TaskService($repository);

        $this->assertSame($paginator, $service->list($listQuery));
    }

    public function test_update_delegates_to_repository(): void
    {
        $task = new Task;
        $task->id = 5;

        $taskData = new UpdateTask(
            title: 'Обновлено',
            description: null,
            dueDate: CarbonImmutable::parse('2025-01-25T18:00:00'),
            status: TaskStatus::Completed,
            priority: TaskPriority::Low,
            category: 'Дом',
        );

        $updatedTask = new Task;
        $updatedTask->id = 5;

        $repository = Mockery::mock(TaskRepositoryContract::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(5)
            ->andReturn($task);
        $repository->shouldReceive('update')
            ->once()
            ->with($task, $taskData)
            ->andReturn($updatedTask);

        $service = new TaskService($repository);

        $this->assertSame($updatedTask, $service->update(5, $taskData));
    }

    public function test_delete_delegates_to_repository(): void
    {
        $task = new Task;
        $task->id = 3;

        $repository = Mockery::mock(TaskRepositoryContract::class);
        $repository->shouldReceive('findById')
            ->once()
            ->with(3)
            ->andReturn($task);
        $repository->shouldReceive('delete')
            ->once()
            ->with($task);

        $service = new TaskService($repository);

        $service->delete(3);

        $this->addToAssertionCount(1);
    }
}
