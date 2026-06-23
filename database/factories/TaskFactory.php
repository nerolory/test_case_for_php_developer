<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Фабрика тестовых задач.
 *
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createDate = now();

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'due_date' => $createDate->copy()->addDays(3),
            'create_date' => $createDate,
            'status' => TaskStatus::Pending,
            'priority' => TaskPriority::Medium,
            'category' => 'Работа',
        ];
    }
}
