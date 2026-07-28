<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_production_tasks', function (Blueprint $table) {
            $table->foreignId('outsourced_staff_id')
                ->nullable()
                ->after('staff_id')
                ->constrained('outsourced_staff')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('post_production_tasks', function (Blueprint $table) {
            $table->dropForeign(['outsourced_staff_id']);
            $table->dropColumn('outsourced_staff_id');
        });
    }
};
