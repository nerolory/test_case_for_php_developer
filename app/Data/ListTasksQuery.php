<?php

declare(strict_types=1);

namespace App\Data;

/**
 * Параметры постраничного списка задач с поиском и сортировкой.
 */
final readonly class ListTasksQuery
{
    public const int DEFAULT_PAGE = 1;

    public const int DEFAULT_PER_PAGE = 10;

    public const int MAX_PER_PAGE = 100;

    /**
     * @param  string|null  $search  Подстрока для поиска по title или null
     * @param  string|null  $sort  Поле сортировки (`due_date`, `created_at`) или null для id desc
     * @param  int  $page  Номер страницы (с 1)
     * @param  int  $perPage  Размер страницы
     */
    public function __construct(
        public ?string $search,
        public ?string $sort,
        public int $page,
        public int $perPage,
    ) {}
}
