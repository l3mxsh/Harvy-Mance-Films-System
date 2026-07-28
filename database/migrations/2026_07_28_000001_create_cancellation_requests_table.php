<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancellation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->integer('refund_percentage')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'refunded'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->string('refund_reference')->nullable();
            $table->string('refund_proof')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellation_requests');
    }
};
