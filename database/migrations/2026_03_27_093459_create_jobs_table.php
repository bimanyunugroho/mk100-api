<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TypeJobEnum;
use App\Enums\StatusJobEnum;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('employer_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('salary_range', 100)->nullable()->comment('Contohnya: 7.000.000 - 8.000.000');
            $table->string('location', 150)->nullable();
            $table->enum('type', TypeJobEnum::values())->default(TypeJobEnum::FREELANCER->value);
            $table->enum('status', StatusJobEnum::values())
                ->default(StatusJobEnum::DRAFT->value)
                ->index()
                ->comment('draft: masih draft | published: terlihat ke freelancer | closed: nggak bisa dilamar');
            $table->timestamp('published_at')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->index('employer_id');
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
