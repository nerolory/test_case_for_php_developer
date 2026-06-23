<?php

declare(strict_types=1);

namespace App\Support;

use App\Contracts\TaskListCacheContract;
use App\Data\ListTasksQuery;
use App\Models\Task;
use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * Кэш списка, поисковых выборок и отдельных задач с тегированием для Redis.
 */
final class TaskListCache implements TaskListCacheContract
{
    /**
     * Возвращает страницу списка из кэша или вычисляет и сохраняет.
     *
     * @param  Closure(): LengthAwarePaginator<int, Task>  $resolver
     * @return LengthAwarePaginator<int, Task>
     */
    public function remember(ListTasksQuery $listQuery, Closure $resolver): LengthAwarePaginator
    {
        if (! $this->isEnabled()) {
            return $resolver();
        }

        /** @var LengthAwarePaginator<int, Task> */
        return Cache::tags($this->tags())->remember(
            $this->listKey($listQuery),
            $this->ttl(),
            $resolver,
        );
    }

    /**
     * Возвращает задачу по id из кэша или загружает из базы.
     *
     * @param  Closure(): ?Task  $resolver
     */
    public function rememberShow(int $taskId, Closure $resolver): ?Task
    {
        if (! $this->isEnabled()) {
            return $resolver();
        }

        /** @var ?Task */
        return Cache::remember(
            $this->showKey($taskId),
            $this->ttl(),
            $resolver,
        );
    }

    /**
     * Сбрасывает закэшированные списки и поисковые выборки (общий тег).
     */
    public function flush(): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        Cache::tags($this->tags())->flush();
    }

    /**
     * Сбрасывает кэш одной задачи; кэш остальных записей не затрагивается.
     */
    public function forgetShow(int $taskId): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        Cache::forget($this->showKey($taskId));
    }

    /**
     * Теги кэша списка. При auth можно добавить, например, tasks:user:{id}.
     *
     * @return list<string>
     */
    private function tags(): array
    {
        return [Config::string('cache.task_list.tag')];
    }

    private function isEnabled(): bool
    {
        if (! Config::boolean('cache.task_list.enabled')) {
            return false;
        }

        return Cache::getStore() instanceof TaggableStore;
    }

    private function ttl(): int
    {
        return Config::integer('cache.task_list.ttl');
    }

    private function listKey(ListTasksQuery $listQuery): string
    {
        $payload = json_encode([
            'search' => $listQuery->search,
            'sort' => $listQuery->sort,
            'page' => $listQuery->page,
            'per_page' => $listQuery->perPage,
        ], JSON_THROW_ON_ERROR);

        return 'tasks:list:'.hash('xxh128', $payload);
    }

    private function showKey(int $taskId): string
    {
        return 'tasks:show:'.$taskId;
    }
}
