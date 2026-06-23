<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\ListTasksQuery;
use App\Models\Task;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Кэш списка и отдельных задач.
 */
interface TaskListCacheContract
{
    /**
     * @param  Closure(): LengthAwarePaginator<int, Task>  $resolver
     * @return LengthAwarePaginator<int, Task>
     */
    public function remember(ListTasksQuery $listQuery, Closure $resolver): LengthAwarePaginator;

    /**
     * @param  Closure(): ?Task  $resolver
     */
    public function rememberShow(int $taskId, Closure $resolver): ?Task;

    public function flush(): void;

    public function forgetShow(int $taskId): void;
}
