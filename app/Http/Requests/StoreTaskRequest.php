<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\CreateTask;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Support\FormDataCaster;
use App\Support\MoscowDateParser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Валидация запроса на создание задачи.
 */
final class StoreTaskRequest extends FormRequest
{
    /**
     * Разрешает создание задачи без авторизации.
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
            'create_date' => ['nullable', 'date_format:Y-m-d\TH:i:s'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'category' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Подставляет доменную дату создания, если клиент её не передал.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->filled('create_date')) {
            $this->merge([
                'create_date' => now()->format('Y-m-d\TH:i:s'),
            ]);
        }
    }

    /**
     * Формирует данные создания для сервисного слоя.
     */
    public function toCreateTask(): CreateTask
    {
        /** @var array<string, mixed> $validated */
        $validated = $this->validated();

        $title = FormDataCaster::string($validated, 'title');
        $description = FormDataCaster::nullableString($validated, 'description');
        $dueDate = MoscowDateParser::parse(FormDataCaster::string($validated, 'due_date'));
        $createDate = MoscowDateParser::parse(FormDataCaster::string($validated, 'create_date'));
        $priority = TaskPriority::from(FormDataCaster::string($validated, 'priority'));
        $category = FormDataCaster::string($validated, 'category');
        $status = array_key_exists('status', $validated)
            ? TaskStatus::from(FormDataCaster::string($validated, 'status'))
            : TaskStatus::Pending;

        return new CreateTask(
            title: $title,
            description: $description,
            dueDate: $dueDate,
            createDate: $createDate,
            status: $status,
            priority: $priority,
            category: $category,
        );
    }
}
