<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Модель задачи — сущность хранения данных в базе.
 *
 * @property int $id
 * @property string $title
 * @property string $title_lower
 * @property string|null $description
 * @property Carbon $due_date
 * @property Carbon $create_date
 * @property TaskStatus $status
 * @property TaskPriority $priority
 * @property string $category
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'create_date',
        'status',
        'priority',
        'category',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'create_date' => 'datetime',
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Task $task): void {
            $task->title_lower = mb_strtolower($task->title, 'UTF-8');
        });
    }
}
