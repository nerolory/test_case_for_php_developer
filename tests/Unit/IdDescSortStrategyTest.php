<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Task;
use App\Sorting\IdDescSortStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class IdDescSortStrategyTest extends TestCase
{
    use RefreshDatabase;

    public function test_sorts_by_id_descending(): void
    {
        $first = Task::factory()->create(['title' => 'Первая']);
        $second = Task::factory()->create(['title' => 'Вторая']);

        $query = Task::query();
        (new IdDescSortStrategy)->apply($query);

        $this->assertSame([$second->id, $first->id], $query->pluck('id')->all());
    }
}
