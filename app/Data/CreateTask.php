<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Carbon\CarbonInterface;

/**
 * Данные для создания новой задачи.
 */
final readonly class CreateTask extends TaskWriteData
{
    /**
     * @param  string  $title  Заголовок задачи
     * @param  string|null  $description  Описание или null
     * @param  CarbonInterface  $dueDate  Срок выполнения
     * @param  CarbonInterface  $createDate  Дата создания задачи
     * @param  TaskStatus  $status  Статус выполнения
     * @param  TaskPriority  $priority  Приоритет
     * @param  string  $category  Категория (нормализуется в {@see toArray()})
     */
    public function __construct(
        string $title,
        ?string $description,
        CarbonInterface $dueDate,
        public CarbonInterface $createDate,
        TaskStatus $status,
        TaskPriority $priority,
        string $category,
    ) {
        parent::__construct($title, $description, $dueDate, $status, $priority, $category);
    }

    /**
     * @return array{
     *     title: string,
     *     description: string|null,
     *     due_date: CarbonInterface,
     *     create_date: CarbonInterface,
     *     status: TaskStatus,
     *     priority: TaskPriority,
     *     category: string
     * }
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'create_date' => $this->createDate,
        ];
    }
}
