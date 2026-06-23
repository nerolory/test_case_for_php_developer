<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\TaskRepositoryContract;
use App\Contracts\TaskServiceContract;
use App\Data\CreateTask;
use App\Data\ListTasksQuery;
use App\Data\UpdateTask;
use App\Exceptions\TaskNotFoundException;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Сервис бизнес-операций над задачами.
 */
final class TaskService implements TaskServiceContract
{
    /**
     * @param  TaskRepositoryContract  $taskRepository  Доступ к данным задач
     */
    public function __construct(
        private readonly TaskRepositoryContract $taskRepository,
    ) {}

    /**
     * Создаёт новую задачу.
     *
     * @param  CreateTask  $taskData  Проверенные данные новой задачи
     */
    public function create(CreateTask $taskData): Task
    {
        return $this->taskRepository->create($taskData);
    }

    /**
     * Возвращает задачу по идентификатору.
     *
     * @param  int  $taskId  Идентификатор задачи
     *
     * @throws TaskNotFoundException Если задача с указанным id отсутствует
     */
    public function getById(int $taskId): Task
    {
        $task = $this->taskRepository->findById($taskId);

        if ($task === null) {
            throw new TaskNotFoundException;
        }

        return $task;
    }

    /**
     * Возвращает постраничный список задач.
     *
     * @param  ListTasksQuery  $listQuery  Фильтры, сортировка и параметры страницы
     * @return LengthAwarePaginator<int, Task>
     */
    public function list(ListTasksQuery $listQuery): LengthAwarePaginator
    {
        return $this->taskRepository->paginate($listQuery);
    }

    /**
     * Полностью обновляет задачу.
     *
     * @param  int  $taskId  Идентификатор обновляемой задачи
     * @param  UpdateTask  $taskData  Полный набор новых атрибутов
     *
     * @throws TaskNotFoundException Если задача с указанным id отсутствует
     */
    public function update(int $taskId, UpdateTask $taskData): Task
    {
        $task = $this->getById($taskId);

        return $this->taskRepository->update($task, $taskData);
    }

    /**
     * Жёстко удаляет задачу.
     *
     * @param  int  $taskId  Идентификатор удаляемой задачи
     *
     * @throws TaskNotFoundException Если задача с указанным id отсутствует
     */
    public function delete(int $taskId): void
    {
        $task = $this->getById($taskId);
        $this->taskRepository->delete($task);
    }
}
