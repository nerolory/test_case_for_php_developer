<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Типизированный ответ после успешного создания задачи.
 */
final class TaskCreatedResource extends JsonResource
{
    /**
     * @param  int  $id  Идентификатор созданной задачи.
     * @param  string  $message  Текст сообщения для клиента.
     */
    public function __construct(
        private readonly int $id,
        private readonly string $message = 'Task created successfully',
    ) {
        parent::__construct(null);
    }

    /**
     * @return array{id: int, message: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
        ];
    }
}
