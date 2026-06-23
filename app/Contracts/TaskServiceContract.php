<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\CreateTask;
use App\Data\ListTasksQuery;
use App\Data\UpdateTask;
use App\Exceptions\TaskNotFoundException;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Контракт бизнес-операций над задачами.
 */
interface TaskServiceContract
{
    /**
     * Создаёт новую задачу.
     *
     * @param  CreateTask  $taskData  Проверенные данные новой задачи
     */
    public function create(CreateTask $taskData): Task;

    /**
     * Возвращает задачу по идентификатору.
     *
     * @param  int  $taskId  Идентификатор задачи
     *
     * @throws TaskNotFoundException Если задача с указанным id отсутствует
     */
    public function getById(int $taskId): Task;

    /**
     * Возвращает постраничный список задач.
     *
     * @param  ListTasksQuery  $listQuery  Фильтры, сортировка и параметры страницы
     * @return LengthAwarePaginator<int, Task>
     */
    public function list(ListTasksQuery $listQuery): LengthAwarePaginator;

    /**
     * Полностью обновляет задачу.
     *
     * @param  int  $taskId  Идентификатор обновляемой задачи
     * @param  UpdateTask  $taskData  Полный набор новых атрибутов
     *
     * @throws TaskNotFoundException Если задача с указанным id отсутствует
     */
    public function update(int $taskId, UpdateTask $taskData): Task;

    /**
     * Жёстко удаляет задачу.
     *
     * @param  int  $taskId  Идентификатор удаляемой задачи
     *
     * @throws TaskNotFoundException Если задача с указанным id отсутствует
     */
    public function delete(int $taskId): void;
}
