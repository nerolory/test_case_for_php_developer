<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\TaskListCacheContract;
use App\Contracts\TaskRepositoryContract;
use App\Data\CreateTask;
use App\Data\ListTasksQuery;
use App\Data\UpdateTask;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Декоратор репозитория: кэширует чтение, сбрасывает кэш при изменениях.
 */
final class CachingTaskRepository implements TaskRepositoryContract
{
    /**
     * @param  TaskRepositoryContract  $repository  Доступ к данным без кэша
     * @param  TaskListCacheContract  $taskListCache  Кэш списка и отдельных задач
     */
    public function __construct(
        private readonly TaskRepositoryContract $repository,
        private readonly TaskListCacheContract $taskListCache,
    ) {}

    public function create(CreateTask $taskData): Task
    {
        $task = $this->repository->create($taskData);
        $this->taskListCache->flush();

        return $task;
    }

    public function findById(int $taskId): ?Task
    {
        return $this->taskListCache->rememberShow(
            $taskId,
            fn (): ?Task => $this->repository->findById($taskId),
        );
    }

    public function paginate(ListTasksQuery $listQuery): LengthAwarePaginator
    {
        return $this->taskListCache->remember(
            $listQuery,
            fn (): LengthAwarePaginator => $this->repository->paginate($listQuery),
        );
    }

    public function update(Task $task, UpdateTask $taskData): Task
    {
        $updated = $this->repository->update($task, $taskData);
        $this->taskListCache->flush();
        $this->taskListCache->forgetShow($task->id);

        return $updated;
    }

    public function delete(Task $task): void
    {
        $this->repository->delete($task);
        $this->taskListCache->flush();
        $this->taskListCache->forgetShow($task->id);
    }
}
