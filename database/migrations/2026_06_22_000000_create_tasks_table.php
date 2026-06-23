<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создаёт таблицу задач с индексами под поиск, сортировку и фильтрацию.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('title_lower');
            $table->text('description')->nullable();
            $table->dateTime('due_date');
            $table->dateTime('create_date');
            $table->string('status', 32);
            $table->string('priority', 32);
            $table->string('category');
            $table->timestamps();

            $table->index('title_lower');
            $table->index('due_date');
            $table->index('create_date');
            $table->index('status');
            $table->index('priority');
            $table->index('category');
        });
    }

    /**
     * Удаляет таблицу задач.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
