<?php

declare(strict_types=1);

namespace App\OpenApi;

use App\Data\ListTasksQuery;
use OpenApi\Attributes as OA;

/**
 * Описание HTTP-операций над задачами для Swagger.
 */
#[OA\Tag(name: 'Tasks', description: 'Управление задачами')]
final class TaskEndpoints
{
    #[OA\Get(
        path: '/api/tasks',
        operationId: 'listTasks',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sort', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['due_date', 'created_at'])),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, default: ListTasksQuery::DEFAULT_PAGE)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: ListTasksQuery::MAX_PER_PAGE, default: ListTasksQuery::DEFAULT_PER_PAGE)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Список задач'),
            new OA\Response(response: 422, description: 'Ошибка валидации'),
        ]
    )]
    public function list(): void {}

    #[OA\Post(
        path: '/api/tasks',
        operationId: 'createTask',
        tags: ['Tasks'],
        responses: [
            new OA\Response(response: 201, description: 'Задача создана'),
            new OA\Response(response: 422, description: 'Ошибка валидации'),
        ]
    )]
    public function create(): void {}

    #[OA\Get(
        path: '/api/tasks/{id}',
        operationId: 'showTask',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Задача найдена'),
            new OA\Response(response: 404, description: 'Задача не найдена'),
        ]
    )]
    public function show(): void {}

    #[OA\Put(
        path: '/api/tasks/{id}',
        operationId: 'updateTask',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Задача обновлена'),
            new OA\Response(response: 404, description: 'Задача не найдена'),
            new OA\Response(response: 422, description: 'Ошибка валидации'),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/tasks/{id}',
        operationId: 'deleteTask',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Задача удалена'),
            new OA\Response(response: 404, description: 'Задача не найдена'),
        ]
    )]
    public function delete(): void {}
}
