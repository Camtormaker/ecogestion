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
        Schema::create('collection_points', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('type', ['educativo', 'comunidad', 'empresa']);
            $table->text('address');
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_points');
    }
};
