<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\UpdateTask;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Support\FormDataCaster;
use App\Support\MoscowDateParser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Валидация полного обновления задачи.
 */
final class UpdateTaskRequest extends FormRequest
{
    /**
     * Разрешает обновление задачи без авторизации.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date_format:Y-m-d\TH:i:s'],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'category' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Формирует данные обновления для сервисного слоя.
     */
    public function toUpdateTask(): UpdateTask
    {
        /** @var array<string, mixed> $validated */
        $validated = $this->validated();

        return new UpdateTask(
            title: FormDataCaster::string($validated, 'title'),
            description: FormDataCaster::nullableString($validated, 'description'),
            dueDate: MoscowDateParser::parse(FormDataCaster::string($validated, 'due_date')),
            status: TaskStatus::from(FormDataCaster::string($validated, 'status')),
            priority: TaskPriority::from(FormDataCaster::string($validated, 'priority')),
            category: FormDataCaster::string($validated, 'category'),
        );
    }
}
