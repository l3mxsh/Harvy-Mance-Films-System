<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('final_payment_status')->nullable()->after('payment_status');
            $table->boolean('deliverables_unlocked')->default(false)->after('final_payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['final_payment_status', 'deliverables_unlocked']);
        });
    }
};
