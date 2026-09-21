<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->string('status')->default('reserved')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'ongoing', 'completed', 'cancelled'])->default('pending')->change();
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->enum('status', ['reserved', 'in_use', 'returned'])->default('reserved')->change();
        });
    }
};