<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['equipment', 'material']);
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('unit')->default('pcs');
            $table->enum('condition_status', ['new', 'good', 'maintenance', 'damaged'])->default('good');
            $table->enum('availability_status', ['available', 'in_use', 'reserved', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
