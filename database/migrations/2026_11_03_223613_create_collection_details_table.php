<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collection_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_event_id')->constrained('collection_events')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('restrict');
            $table->decimal('weight_kg', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_details');
    }
};
