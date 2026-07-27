<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_production_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_production_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('task_type');
            $table->text('instructions')->nullable();
            $table->string('status')->default('not_started');
            $table->text('deliverable_link')->nullable();
            $table->text('remarks')->nullable();
            $table->text('revision_notes')->nullable();
            $table->string('admin_review_status')->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_production_tasks');
    }
};
