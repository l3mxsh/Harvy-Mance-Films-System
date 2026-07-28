<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outsourced_staff', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
        });

        Schema::create('outsourced_staff_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outsourced_staff_id')->constrained('outsourced_staff')->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['outsourced_staff_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outsourced_staff_team');
        Schema::table('outsourced_staff', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
