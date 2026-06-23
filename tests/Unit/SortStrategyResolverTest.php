<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Task;
use App\Sorting\CreateDateSortStrategy;
use App\Sorting\DueDateSortStrategy;
use App\Sorting\IdDescSortStrategy;
use App\Sorting\SortStrategyResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SortStrategyResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_default_strategy_as_id_desc(): void
    {
        $resolver = $this->app->make(SortStrategyResolver::class);

        $this->assertInstanceOf(IdDescSortStrategy::class, $resolver->resolve(null));
    }

    public function test_resolves_due_date_strategy(): void
    {
        $resolver = $this->app->make(SortStrategyResolver::class);

        $this->assertInstanceOf(DueDateSortStrategy::class, $resolver->resolve('due_date'));
    }

    public function test_resolves_created_at_strategy(): void
    {
        $resolver = $this->app->make(SortStrategyResolver::class);

        $this->assertInstanceOf(CreateDateSortStrategy::class, $resolver->resolve('created_at'));
    }

    public function test_due_date_strategy_sorts_ascending(): void
    {
        Task::factory()->create(['title' => 'Поздняя', 'due_date' => '2025-02-01T10:00:00']);
        Task::factory()->create(['title' => 'Ранняя', 'due_date' => '2025-01-01T10:00:00']);

        $query = Task::query();
        $this->app->make(DueDateSortStrategy::class)->apply($query);

        $titles = $query->pluck('title')->all();

        $this->assertSame(['Ранняя', 'Поздняя'], $titles);
    }
}
