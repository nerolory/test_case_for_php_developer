<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Cache\RedisStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

final class TaskListCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('redis')) {
            $this->fail(
                'Для cache-тестов требуется расширение redis. Запускайте: docker compose exec app php artisan test',
            );
        }

        config([
            'cache.default' => 'redis',
            'cache.task_list.enabled' => true,
        ]);

        $this->app->forgetInstance('cache');
        $this->app->forgetInstance('cache.store');

        if (! Cache::getStore() instanceof RedisStore) {
            $this->fail(
                'Для cache-тестов требуется Redis (CACHE_STORE=redis). Запускайте: docker compose exec app php artisan test',
            );
        }

        Cache::flush();
    }

    protected function tearDown(): void
    {
        if (Cache::getStore() instanceof RedisStore) {
            Cache::flush();
        }

        config([
            'cache.default' => 'array',
            'cache.task_list.enabled' => false,
        ]);

        $this->app->forgetInstance('cache');
        $this->app->forgetInstance('cache.store');

        parent::tearDown();
    }

    public function test_list_works_when_redis_cache_enabled(): void
    {
        Task::factory()->create(['title' => 'Кэшируемая']);

        $this->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Кэшируемая']);
    }

    public function test_create_invalidates_tasks_list_cache_tag(): void
    {
        $tag = (string) config('cache.task_list.tag');
        Cache::tags([$tag])->put('tasks:list:probe', 'stale', 60);

        $this->postJson('/api/tasks', [
            'title' => 'Новая после сброса кэша',
            'due_date' => '2025-01-20T15:00:00',
            'create_date' => '2025-01-20T15:00:00',
            'priority' => 'высокий',
            'category' => 'Работа',
        ])->assertCreated();

        $this->assertFalse(Cache::tags([$tag])->has('tasks:list:probe'));
    }

    public function test_update_forgets_only_updated_task_show_cache(): void
    {
        $unchanged = Task::factory()->create(['title' => 'Без изменений']);
        $updated = Task::factory()->create(['title' => 'Будет изменена']);

        $this->getJson('/api/tasks/'.$unchanged->id)->assertOk();
        $this->getJson('/api/tasks/'.$updated->id)->assertOk();

        $unchangedKey = 'tasks:show:'.$unchanged->id;
        $updatedKey = 'tasks:show:'.$updated->id;

        $this->assertTrue(Cache::has($unchangedKey));
        $this->assertTrue(Cache::has($updatedKey));

        $this->putJson('/api/tasks/'.$updated->id, [
            'title' => 'Изменена',
            'due_date' => '2025-01-25T18:00:00',
            'priority' => 'высокий',
            'category' => 'Работа',
            'status' => 'выполнена',
        ])->assertOk();

        $this->assertTrue(Cache::has($unchangedKey));
        $this->assertFalse(Cache::has($updatedKey));
    }
}
