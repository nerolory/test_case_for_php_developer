<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Статусы выполнения задачи.
 */
enum TaskStatus: string
{
    case Pending = 'не выполнена';
    case Completed = 'выполнена';
}
