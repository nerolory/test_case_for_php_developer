<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Task;
use App\Support\MoscowDateParser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Сериализация задачи для JSON-ответов API.
 *
 * @mixin Task
 */
final class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => MoscowDateParser::format($this->due_date),
            'create_date' => MoscowDateParser::format($this->create_date),
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'category' => $this->category,
        ];
    }
}
