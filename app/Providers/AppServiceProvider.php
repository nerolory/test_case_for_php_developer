<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\TaskListCacheContract;
use App\Contracts\TaskRepositoryContract;
use App\Contracts\TaskServiceContract;
use App\Repositories\CachingTaskRepository;
use App\Repositories\TaskRepository;
use App\Services\TaskService;
use App\Support\TaskListCache;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

/**
 * Регистрация привязок контрактов и глобальные настройки приложения.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Привязывает контракты репозитория и сервиса к реализациям.
     */
    public function register(): void
    {
        $this->app->singleton(TaskRepository::class);
        $this->app->singleton(TaskListCacheContract::class, TaskListCache::class);
        $this->app->singleton(TaskListCache::class);
        $this->app->bind(TaskRepositoryContract::class, function ($app): CachingTaskRepository {
            return new CachingTaskRepository(
                $app->make(TaskRepository::class),
                $app->make(TaskListCacheContract::class),
            );
        });
        $this->app->bind(TaskServiceContract::class, TaskService::class);
    }

    /**
     * Отключает обёртку data у JSON Resource для соответствия контракту API.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
