<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use InvalidArgumentException;

/**
 * Парсинг и форматирование дат Y-m-d\TH:i:s.
 */
final class MoscowDateParser
{
    private const string FORMAT = 'Y-m-d\TH:i:s';

    /**
     * Преобразует строку даты в объект даты.
     *
     * @throws InvalidArgumentException
     */
    public static function parse(string $value): CarbonInterface
    {
        $date = Carbon::createFromFormat(self::FORMAT, $value, 'Europe/Moscow');

        if ($date === null) {
            throw new InvalidArgumentException('Некорректный формат даты.');
        }

        return $date;
    }

    /**
     * Форматирует дату для ответа API.
     */
    public static function format(CarbonInterface $date): string
    {
        return $date->timezone('Europe/Moscow')->format(self::FORMAT);
    }
}
