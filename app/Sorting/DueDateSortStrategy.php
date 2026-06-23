<?php

declare(strict_types=1);

namespace App\Sorting;

use App\Contracts\SortStrategyContract;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

/**
 * Сортировка по сроку выполнения по возрастанию.
 */
final class DueDateSortStrategy implements SortStrategyContract
{
    /**
     * Упорядочивает выборку по колонке due_date.
     *
     * @param  Builder<Task>  $query  Построитель выборки задач
     */
    public function apply(Builder $query): void
    {
        $query->orderBy('due_date');
    }
}
