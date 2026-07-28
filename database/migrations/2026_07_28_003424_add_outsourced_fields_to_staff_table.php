<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->boolean('is_outsourced')->default(false)->after('status');
            $table->boolean('is_temporary')->default(false)->after('is_outsourced');
            $table->timestamp('temp_expires_at')->nullable()->after('is_temporary');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['is_outsourced', 'is_temporary', 'temp_expires_at']);
        });
    }
};
