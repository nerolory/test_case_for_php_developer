<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Типизированный ответ-сообщение для операций обновления и удаления.
 */
final class OperationMessageResource extends JsonResource
{
    /**
     * @param  string  $message  Текст сообщения для клиента.
     */
    public function __construct(
        private readonly string $message,
    ) {
        parent::__construct(null);
    }

    /**
     * @return array{message: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
