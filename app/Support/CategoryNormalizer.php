<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Нормализует имя категории для единообразного хранения и предотвращения дублей.
 */
final class CategoryNormalizer
{
    /**
     * Приводит категорию к единому виду: обрезка пробелов и заглавная буква каждого слова.
     */
    public static function normalize(string $category): string
    {
        $trimmed = trim($category);

        return mb_convert_case(mb_strtolower($trimmed, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}
