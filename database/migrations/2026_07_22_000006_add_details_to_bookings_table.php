<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_ref')->unique()->after('id');
            $table->string('event_type')->nullable()->after('package_id');
            $table->time('event_time')->nullable()->after('event_date');
            $table->text('event_address')->nullable()->after('event_venue');
            $table->text('event_description')->nullable()->after('event_address');
            $table->decimal('downpayment_amount', 10, 2)->default(0)->after('total_price');
            $table->boolean('terms_agreed')->default(false)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_ref', 'event_type', 'event_time',
                'event_address', 'event_description',
                'downpayment_amount', 'terms_agreed',
            ]);
        });
    }
};
