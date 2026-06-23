<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Support\CategoryNormalizer;
use Carbon\CarbonInterface;

/**
 * Общие поля задачи для операций записи в хранилище.
 */
abstract readonly class TaskWriteData
{
    /**
     * @param  string  $title  Заголовок задачи
     * @param  string|null  $description  Описание или null
     * @param  CarbonInterface  $dueDate  Срок выполнения
     * @param  TaskStatus  $status  Статус выполнения
     * @param  TaskPriority  $priority  Приоритет
     * @param  string  $category  Категория до нормализации
     */
    public function __construct(
        public string $title,
        public ?string $description,
        public CarbonInterface $dueDate,
        public TaskStatus $status,
        public TaskPriority $priority,
        public string $category,
    ) {}

    /**
     * Преобразует данные в атрибуты для {@see Task::fill()}.
     *
     * @return array{
     *     title: string,
     *     description: string|null,
     *     due_date: CarbonInterface,
     *     status: TaskStatus,
     *     priority: TaskPriority,
     *     category: string
     * }
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->dueDate,
            'status' => $this->status,
            'priority' => $this->priority,
            'category' => CategoryNormalizer::normalize($this->category),
        ];
    }
}
