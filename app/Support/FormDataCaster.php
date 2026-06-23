<?php

declare(strict_types=1);

namespace App\Support;

use LogicException;

/**
 * Безопасное извлечение типизированных значений из результата FormRequest::validated().
 *
 * Не выполняет валидацию — только читает уже проверенные поля.
 */
final class FormDataCaster
{
    /**
     * @param  array<string, mixed>  $data  Результат validated() после успешной проверки rules()
     */
    public static function string(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value)) {
            throw new LogicException(sprintf('Ожидалась строка в поле %s.', $key));
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $data  Результат validated() после успешной проверки rules()
     */
    public static function nullableString(array $data, string $key): ?string
    {
        if (! array_key_exists($key, $data) || $data[$key] === null) {
            return null;
        }

        return self::string($data, $key);
    }

    /**
     * @param  array<string, mixed>  $data  Результат validated() после успешной проверки rules()
     */
    public static function int(array $data, string $key, int $default): int
    {
        if (! array_key_exists($key, $data)) {
            return $default;
        }

        $value = $data[$key];

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }

        throw new LogicException(sprintf('Ожидалось целое число в поле %s.', $key));
    }
}
