<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

/**
 * Наполняет базу демонстрационными задачами.
 */
class TaskSeeder extends Seeder
{
    /**
     * Запускает наполнение таблицы задач.
     */
    public function run(): void
    {
        Task::factory()->count(20)->create();
    }
}
