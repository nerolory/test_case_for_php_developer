<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\TaskServiceContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\OperationMessageResource;
use App\Http\Resources\TaskCreatedResource;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;

/**
 * HTTP-контроллер операций над задачами.
 */
final class TaskController extends Controller
{
    /**
     * @param  TaskServiceContract  $taskService  Бизнес-операции над задачами
     */
    public function __construct(
        private readonly TaskServiceContract $taskService,
    ) {}

    /**
     * Возвращает постраничный список задач.
     *
     * @param  ListTasksRequest  $request  Проверенные query-параметры списка
     */
    public function index(ListTasksRequest $request): JsonResponse
    {
        $paginator = $this->taskService->list($request->toListQuery());

        return TaskResource::collection($paginator->items())
            ->response()
            ->withHeaders([
                'X-Total-Count' => (string) $paginator->total(),
                'X-Per-Page' => (string) $paginator->perPage(),
                'X-Current-Page' => (string) $paginator->currentPage(),
                'X-Last-Page' => (string) $paginator->lastPage(),
            ]);
    }

    /**
     * Создаёт новую задачу.
     *
     * @param  StoreTaskRequest  $request  Проверенное тело запроса создания
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->toCreateTask());

        return (new TaskCreatedResource($task->id))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Возвращает задачу по идентификатору.
     *
     * @param  int  $id  Идентификатор задачи из маршрута
     */
    public function show(int $id): JsonResponse
    {
        $task = $this->taskService->getById($id);

        return (new TaskResource($task))->response();
    }

    /**
     * Полностью обновляет задачу.
     *
     * @param  UpdateTaskRequest  $request  Проверенное тело запроса обновления
     * @param  int  $id  Идентификатор задачи из маршрута
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $this->taskService->update($id, $request->toUpdateTask());

        return (new OperationMessageResource('Task updated successfully'))->response();
    }

    /**
     * Удаляет задачу.
     *
     * @param  int  $id  Идентификатор задачи из маршрута
     */
    public function destroy(int $id): JsonResponse
    {
        $this->taskService->delete($id);

        return (new OperationMessageResource('Task deleted successfully'))->response();
    }
}
