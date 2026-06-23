<?php

declare(strict_types=1);

namespace App\Sorting;

use App\Contracts\SortStrategyContract;

/**
 * Выбирает стратегию сортировки по параметру запроса.
 */
final class SortStrategyResolver
{
    /**
     * @param  DueDateSortStrategy  $dueDateSortStrategy  Сортировка по due_date
     * @param  CreateDateSortStrategy  $createDateSortStrategy  Сортировка по create_date
     * @param  IdDescSortStrategy  $idDescSortStrategy  Сортировка по id desc (по умолчанию)
     */
    public function __construct(
        private readonly DueDateSortStrategy $dueDateSortStrategy,
        private readonly CreateDateSortStrategy $createDateSortStrategy,
        private readonly IdDescSortStrategy $idDescSortStrategy,
    ) {}

    /**
     * Возвращает стратегию для указанного поля сортировки.
     *
     * Параметр `created_at` сортирует по колонке `create_date`.
     * Без параметра sort применяется сортировка по id по убыванию.
     *
     * @param  string|null  $sort  Имя поля сортировки из query-параметра
     */
    public function resolve(?string $sort): SortStrategyContract
    {
        return match ($sort) {
            'due_date' => $this->dueDateSortStrategy,
            'created_at' => $this->createDateSortStrategy,
            default => $this->idDescSortStrategy,
        };
    }
}
