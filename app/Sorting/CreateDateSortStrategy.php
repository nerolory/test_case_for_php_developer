<?php

declare(strict_types=1);

namespace App\Sorting;

use App\Contracts\SortStrategyContract;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

/**
 * Сортировка по дате создания (колонка create_date) по возрастанию.
 *
 * Параметр API `created_at` соответствует полю create_date, а не служебной метке Laravel.
 */
final class CreateDateSortStrategy implements SortStrategyContract
{
    /**
     * Упорядочивает выборку по колонке create_date.
     *
     * @param  Builder<Task>  $query  Построитель выборки задач
     */
    public function apply(Builder $query): void
    {
        $query->orderBy('create_date');
    }
}
