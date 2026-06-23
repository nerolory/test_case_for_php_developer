<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'title' => 'Задача1',
            'description' => 'Задача1 описание',
            'due_date' => '2025-01-20T15:00:00',
            'create_date' => '2025-01-20T15:00:00',
            'priority' => TaskPriority::High->value,
            'category' => 'Работа',
            'status' => TaskStatus::Pending->value,
        ];
    }

    public function test_can_create_task(): void
    {
        $response = $this->postJson('/api/tasks', $this->validPayload());

        $response->assertCreated()
            ->assertJsonStructure(['id', 'message'])
            ->assertJsonPath('message', 'Task created successfully');

        $this->assertDatabaseHas('tasks', [
            'id' => $response->json('id'),
            'title' => 'Задача1',
            'category' => 'Работа',
        ]);
    }

    public function test_create_validation_fails_for_invalid_payload(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => '',
            'due_date' => 'invalid',
        ]);

        $response->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_can_list_tasks_with_pagination_headers(): void
    {
        Task::factory()->count(12)->create();

        $response = $this->getJson('/api/tasks?per_page=10');

        $response->assertOk()
            ->assertHeader('X-Total-Count', '12')
            ->assertHeader('X-Per-Page', '10')
            ->assertHeader('X-Current-Page', '1')
            ->assertHeader('X-Last-Page', '2');

        $this->assertCount(10, $response->json());
    }

    public function test_can_search_tasks_by_title(): void
    {
        Task::factory()->create(['title' => 'Задача1 уникальная']);
        Task::factory()->create(['title' => 'Другая']);

        $response = $this->getJson('/api/tasks?search=уникальная');

        $response->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertSame('Задача1 уникальная', $response->json('0.title'));
    }

    public function test_can_show_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson('/api/tasks/'.$task->id);

        $response->assertOk()
            ->assertJsonPath('id', $task->id)
            ->assertJsonPath('title', $task->title);
    }

    public function test_show_returns_not_found(): void
    {
        $response = $this->getJson('/api/tasks/999');

        $response->assertNotFound()
            ->assertJson(['message' => 'Задача не найдена.']);
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create([
            'create_date' => '2025-01-10T10:00:00',
            'due_date' => '2025-01-20T15:00:00',
        ]);

        $response = $this->putJson('/api/tasks/'.$task->id, [
            'title' => 'Задача2',
            'description' => 'Задача2 описание обновленное',
            'due_date' => '2025-01-25T18:00:00',
            'priority' => TaskPriority::Low->value,
            'category' => 'Дом',
            'status' => TaskStatus::Completed->value,
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Task updated successfully']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Задача2',
            'category' => 'Дом',
        ]);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson('/api/tasks/'.$task->id);

        $response->assertOk()
            ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_can_create_task_with_due_date_before_create_date(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Выполнена ранее',
            'due_date' => '2025-01-01T10:00:00',
            'create_date' => '2025-01-20T15:00:00',
            'status' => TaskStatus::Completed->value,
            'priority' => TaskPriority::High->value,
            'category' => 'Работа',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'id' => $response->json('id'),
            'title' => 'Выполнена ранее',
        ]);
    }

    public function test_update_returns_not_found_for_missing_task(): void
    {
        $response = $this->putJson('/api/tasks/9999', [
            'title' => 'Задача',
            'description' => null,
            'due_date' => '2025-01-25T18:00:00',
            'priority' => TaskPriority::High->value,
            'category' => 'Работа',
            'status' => TaskStatus::Pending->value,
        ]);

        $response->assertNotFound()
            ->assertJson(['message' => 'Задача не найдена.']);
    }

    public function test_delete_returns_not_found_for_missing_task(): void
    {
        $response = $this->deleteJson('/api/tasks/9999');

        $response->assertNotFound()
            ->assertJson(['message' => 'Задача не найдена.']);
    }

    public function test_list_rejects_invalid_sort(): void
    {
        $response = $this->getJson('/api/tasks?sort=invalid');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['sort']);
    }

    public function test_list_sorts_by_created_at_ascending(): void
    {
        Task::factory()->create([
            'title' => 'Поздняя',
            'create_date' => '2025-02-01T10:00:00',
        ]);
        Task::factory()->create([
            'title' => 'Ранняя',
            'create_date' => '2025-01-01T10:00:00',
        ]);

        $response = $this->getJson('/api/tasks?sort=created_at');

        $response->assertOk();
        $this->assertSame('Ранняя', $response->json('0.title'));
        $this->assertSame('Поздняя', $response->json('1.title'));
    }

    public function test_list_sorts_by_due_date_ascending(): void
    {
        Task::factory()->create([
            'title' => 'Поздний срок',
            'due_date' => '2025-03-01T10:00:00',
        ]);
        Task::factory()->create([
            'title' => 'Ранний срок',
            'due_date' => '2025-01-01T10:00:00',
        ]);

        $response = $this->getJson('/api/tasks?sort=due_date');

        $response->assertOk();
        $this->assertSame('Ранний срок', $response->json('0.title'));
        $this->assertSame('Поздний срок', $response->json('1.title'));
    }

    public function test_create_rejects_invalid_status(): void
    {
        $payload = $this->validPayload();
        $payload['status'] = 'pending';

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_create_rejects_invalid_priority(): void
    {
        $payload = $this->validPayload();
        $payload['priority'] = 'urgent';

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['priority']);
    }

    public function test_update_rejects_invalid_status(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson('/api/tasks/'.$task->id, [
            'title' => 'Задача',
            'description' => null,
            'due_date' => '2025-01-25T18:00:00',
            'priority' => TaskPriority::High->value,
            'category' => 'Работа',
            'status' => 'pending',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_update_rejects_invalid_priority(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson('/api/tasks/'.$task->id, [
            'title' => 'Задача',
            'description' => null,
            'due_date' => '2025-01-25T18:00:00',
            'priority' => 'urgent',
            'category' => 'Работа',
            'status' => TaskStatus::Pending->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['priority']);
    }

    public function test_list_default_sort_is_id_descending(): void
    {
        $first = Task::factory()->create(['title' => 'Первая']);
        $second = Task::factory()->create(['title' => 'Вторая']);

        $response = $this->getJson('/api/tasks');

        $response->assertOk();
        $this->assertSame($second->id, $response->json('0.id'));
        $this->assertSame($first->id, $response->json('1.id'));
    }

    public function test_list_rejects_per_page_above_max(): void
    {
        $response = $this->getJson('/api/tasks?per_page=101');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_list_returns_empty_array_for_page_beyond_last(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks?per_page=2&page=10');

        $response->assertOk()
            ->assertJson([]);
    }

    public function test_show_returns_not_found_for_non_numeric_id(): void
    {
        $response = $this->getJson('/api/tasks/abc');

        $response->assertNotFound();
    }

    public function test_create_normalizes_category(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Задача',
            'due_date' => '2025-01-20T15:00:00',
            'create_date' => '2025-01-20T15:00:00',
            'priority' => TaskPriority::High->value,
            'category' => 'работа',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'id' => $response->json('id'),
            'category' => 'Работа',
        ]);
    }

    public function test_create_without_status_defaults_to_pending(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Задача',
            'due_date' => '2025-01-20T15:00:00',
            'create_date' => '2025-01-20T15:00:00',
            'priority' => TaskPriority::High->value,
            'category' => 'Работа',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'id' => $response->json('id'),
            'status' => TaskStatus::Pending->value,
        ]);
    }

    public function test_create_without_create_date_uses_moscow_now(): void
    {
        $fixedNow = now();
        $this->travelTo($fixedNow);

        $dueDate = $fixedNow->format('Y-m-d\TH:i:s');

        $response = $this->postJson('/api/tasks', [
            'title' => 'Задача',
            'due_date' => $dueDate,
            'priority' => TaskPriority::High->value,
            'category' => 'Работа',
        ]);

        $response->assertCreated();

        $task = Task::query()->find($response->json('id'));
        $this->assertNotNull($task);
        $this->assertSame($dueDate, $task->create_date->format('Y-m-d\TH:i:s'));
    }

    public function test_update_rejects_empty_title(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson('/api/tasks/'.$task->id, [
            'title' => '',
            'description' => null,
            'due_date' => '2025-01-25T18:00:00',
            'priority' => TaskPriority::High->value,
            'category' => 'Работа',
            'status' => TaskStatus::Pending->value,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_search_is_case_insensitive_for_cyrillic(): void
    {
        Task::factory()->create(['title' => 'ЗАДАЧА Уникальная']);
        Task::factory()->create(['title' => 'Другая']);

        $response = $this->getJson('/api/tasks?search=уникальная');

        $response->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertSame('ЗАДАЧА Уникальная', $response->json('0.title'));
    }
}
