<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/**
 * Базовое описание API для генерации документации Swagger.
 */
#[OA\Info(
    version: '1.0.0',
    title: 'API «Список задач»',
    description: 'REST API для управления списком задач'
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Локальное окружение (Docker)')]
final class ApiDocumentation {}
