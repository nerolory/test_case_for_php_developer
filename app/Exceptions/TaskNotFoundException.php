<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Исключение при отсутствии задачи в хранилище.
 */
final class TaskNotFoundException extends RuntimeException
{
    /**
     * Создаёт исключение с сообщением по умолчанию для API 404.
     */
    public function __construct()
    {
        parent::__construct('Задача не найдена.');
    }
}
