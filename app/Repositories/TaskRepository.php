<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\TaskRepositoryContract;
use App\Data\CreateTask;
use App\Data\ListTasksQuery;
use App\Data\UpdateTask;
use App\Models\Task;
use App\Sorting\SortStrategyResolver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Репозиторий задач на Eloquent.
 */
final class TaskRepository implements TaskRepositoryContract
{
    /**
     * @param  SortStrategyResolver  $sortStrategyResolver  Выбор стратегии ORDER BY для списка
     */
    public function __construct(
        private readonly SortStrategyResolver $sortStrategyResolver,
    ) {}

    /**
     * Создаёт задачу в хранилище и возвращает сохранённую модель.
     *
     * @param  CreateTask  $taskData  Проверенные данные новой задачи
     */
    public function create(CreateTask $taskData): Task
    {
        $task = new Task;
        $task->fill($taskData->toArray());
        $task->save();

        return $task->refresh();
    }

    /**
     * Ищет задачу по первичному ключу.
     *
     * @param  int  $taskId  Идентификатор задачи
     */
    public function findById(int $taskId): ?Task
    {
        return Task::query()->find($taskId);
    }

    /**
     * Возвращает страницу списка с поиском и сортировкой.
     *
     * @param  ListTasksQuery  $listQuery  Фильтры, сортировка и параметры страницы
     * @return LengthAwarePaginator<int, Task>
     */
    public function paginate(ListTasksQuery $listQuery): LengthAwarePaginator
    {
        $query = Task::query();

        if ($listQuery->search !== null && $listQuery->search !== '') {
            $this->applySearch($query, $listQuery->search);
        }

        $this->sortStrategyResolver->resolve($listQuery->sort)->apply($query);

        return $query->paginate(
            perPage: $listQuery->perPage,
            page: $listQuery->page,
        );
    }

    /**
     * Заменяет атрибуты задачи и сохраняет изменения.
     *
     * @param  Task  $task  Обновляемая модель
     * @param  UpdateTask  $taskData  Полный набор новых атрибутов
     */
    public function update(Task $task, UpdateTask $taskData): Task
    {
        $task->fill($taskData->toArray());
        $task->save();

        return $task->refresh();
    }

    /**
     * Физически удаляет запись задачи.
     *
     * @param  Task  $task  Удаляемая модель
     */
    public function delete(Task $task): void
    {
        $task->delete();
    }

    /**
     * Добавляет регистронезависимый поиск по заголовку.
     *
     * @param  Builder<Task>  $query  Построитель выборки
     * @param  string  $search  Подстрока для поиска в title
     */
    private function applySearch(Builder $query, string $search): void
    {
        $query->where('title_lower', 'like', '%'.mb_strtolower($search, 'UTF-8').'%');
    }
}
