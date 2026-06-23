<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\CreateTask;
use App\Data\ListTasksQuery;
use App\Data\UpdateTask;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Контракт доступа к данным задач.
 */
interface TaskRepositoryContract
{
    /**
     * Создаёт задачу и возвращает сохранённую модель.
     *
     * @param  CreateTask  $taskData  Проверенные данные новой задачи
     */
    public function create(CreateTask $taskData): Task;

    /**
     * Возвращает задачу по идентификатору или null, если запись отсутствует.
     *
     * @param  int  $taskId  Идентификатор задачи
     */
    public function findById(int $taskId): ?Task;

    /**
     * Возвращает постраничный список задач с учётом поиска и сортировки.
     *
     * @param  ListTasksQuery  $listQuery  Фильтры, сортировка и параметры страницы
     * @return LengthAwarePaginator<int, Task>
     */
    public function paginate(ListTasksQuery $listQuery): LengthAwarePaginator;

    /**
     * Обновляет задачу и возвращает актуальное состояние.
     *
     * @param  Task  $task  Существующая модель задачи
     * @param  UpdateTask  $taskData  Полный набор новых атрибутов
     */
    public function update(Task $task, UpdateTask $taskData): Task;

    /**
     * Жёстко удаляет задачу из хранилища.
     *
     * @param  Task  $task  Удаляемая модель
     */
    public function delete(Task $task): void;
}
