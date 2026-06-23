<?php

declare(strict_types=1);

namespace App\Sorting;

use App\Contracts\SortStrategyContract;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

/**
 * Сортировка по идентификатору по убыванию — порядок по умолчанию.
 */
final class IdDescSortStrategy implements SortStrategyContract
{
    /**
     * Упорядочивает выборку по id в порядке убывания.
     *
     * @param  Builder<Task>  $query  Построитель выборки задач
     */
    public function apply(Builder $query): void
    {
        $query->orderByDesc('id');
    }
}
