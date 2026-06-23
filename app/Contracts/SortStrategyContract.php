<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

/**
 * Контракт стратегии сортировки списка задач.
 */
interface SortStrategyContract
{
    /**
     * Применяет порядок сортировки к запросу.
     *
     * @param  Builder<Task>  $query  Построитель выборки задач
     */
    public function apply(Builder $query): void;
}
