<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('job_id')
                ->references('id')
                ->on('jobs')
                ->restrictOnDelete();
            $table->foreignUlid('freelancer_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->text('cover_letter')->nullable();
            $table->string('cv_path');
            $table->string('cv_original_name');
            $table->string('cv_mime_type', 100);
            $table->unsignedBigInteger('cv_size_bytes');
            $table->timestamps();
            $table->unique(
                ['job_id', 'freelancer_id'],
                'uq_application_job_freelancer'
            );
            $table->index('job_id');
            $table->index('freelancer_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
