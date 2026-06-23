<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\CategoryNormalizer;
use Tests\TestCase;

final class CategoryNormalizerTest extends TestCase
{
    public function test_normalizes_category_to_title_case(): void
    {
        $this->assertSame('Работа', CategoryNormalizer::normalize('  работа '));
        $this->assertSame('Работа', CategoryNormalizer::normalize('РАБОТА'));
    }
}
