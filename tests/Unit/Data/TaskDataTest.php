<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use App\Data\CreateTask;
use App\Data\ListTasksQuery;
use App\Data\UpdateTask;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Carbon\CarbonImmutable;
use Tests\TestCase;

final class TaskDataTest extends TestCase
{
    public function test_create_task_to_array_includes_create_date_and_normalizes_category(): void
    {
        $taskData = new CreateTask(
            title: 'Задача',
            description: 'Описание',
            dueDate: CarbonImmutable::parse('2025-01-25T15:00:00'),
            createDate: CarbonImmutable::parse('2025-01-20T15:00:00'),
            status: TaskStatus::Pending,
            priority: TaskPriority::High,
            category: '  работа ',
        );

        $attributes = $taskData->toArray();

        $this->assertSame('Задача', $attributes['title']);
        $this->assertSame('Работа', $attributes['category']);
        $this->assertEquals(CarbonImmutable::parse('2025-01-20T15:00:00'), $attributes['create_date']);
    }

    public function test_update_task_to_array_normalizes_category(): void
    {
        $taskData = new UpdateTask(
            title: 'Обновлено',
            description: null,
            dueDate: CarbonImmutable::parse('2025-01-25T15:00:00'),
            status: TaskStatus::Completed,
            priority: TaskPriority::Low,
            category: 'дом',
        );

        $attributes = $taskData->toArray();

        $this->assertSame('Дом', $attributes['category']);
        $this->assertArrayNotHasKey('create_date', $attributes);
    }

    public function test_list_tasks_query_pagination_constants(): void
    {
        $this->assertSame(1, ListTasksQuery::DEFAULT_PAGE);
        $this->assertSame(10, ListTasksQuery::DEFAULT_PER_PAGE);
        $this->assertSame(100, ListTasksQuery::MAX_PER_PAGE);
    }
}
