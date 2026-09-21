<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->date('event_date');
            $table->text('event_venue')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->enum('status', ['reserved', 'in_use', 'returned'])->default('reserved');
            $table->timestamps();
        });

        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_addons');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
    }
};
