<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Data\ListTasksQuery;
use App\Support\FormDataCaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Валидация параметров постраничного списка задач.
 */
final class ListTasksRequest extends FormRequest
{
    /**
     * Разрешает получение списка без авторизации.
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
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(['due_date', 'created_at'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:'.ListTasksQuery::MAX_PER_PAGE],
        ];
    }

    /**
     * Формирует объект запроса списка для сервисного слоя.
     */
    public function toListQuery(): ListTasksQuery
    {
        /** @var array<string, mixed> $validated */
        $validated = $this->validated();

        return new ListTasksQuery(
            search: FormDataCaster::nullableString($validated, 'search'),
            sort: FormDataCaster::nullableString($validated, 'sort'),
            page: FormDataCaster::int($validated, 'page', ListTasksQuery::DEFAULT_PAGE),
            perPage: FormDataCaster::int($validated, 'per_page', ListTasksQuery::DEFAULT_PER_PAGE),
        );
    }
}
