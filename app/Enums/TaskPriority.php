<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Приоритеты задачи.
 */
enum TaskPriority: string
{
    case Low = 'низкий';
    case Medium = 'средний';
    case High = 'высокий';
}
