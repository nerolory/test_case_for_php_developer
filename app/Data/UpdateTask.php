<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Carbon\CarbonInterface;

/**
 * Данные для полного обновления существующей задачи.
 */
final readonly class UpdateTask extends TaskWriteData
{
    /**
     * @param  string  $title  Заголовок задачи
     * @param  string|null  $description  Описание или null
     * @param  CarbonInterface  $dueDate  Срок выполнения
     * @param  TaskStatus  $status  Статус выполнения
     * @param  TaskPriority  $priority  Приоритет
     * @param  string  $category  Категория (нормализуется в {@see toArray()})
     */
    public function __construct(
        string $title,
        ?string $description,
        CarbonInterface $dueDate,
        TaskStatus $status,
        TaskPriority $priority,
        string $category,
    ) {
        parent::__construct($title, $description, $dueDate, $status, $priority, $category);
    }
}
