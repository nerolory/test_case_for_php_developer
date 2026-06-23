<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Task;
use App\Sorting\CreateDateSortStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateDateSortStrategyTest extends TestCase
{
    use RefreshDatabase;

    public function test_sorts_by_create_date_ascending(): void
    {
        Task::factory()->create([
            'title' => 'Поздняя',
            'create_date' => '2025-02-01T10:00:00',
        ]);
        Task::factory()->create([
            'title' => 'Ранняя',
            'create_date' => '2025-01-01T10:00:00',
        ]);

        $query = Task::query();
        (new CreateDateSortStrategy)->apply($query);

        $this->assertSame(['Ранняя', 'Поздняя'], $query->pluck('title')->all());
    }
}
