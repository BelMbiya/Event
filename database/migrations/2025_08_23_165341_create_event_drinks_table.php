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
        Schema::create('event_drinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('drink_id')->constrained('drinks')->onDelete('cascade');
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('available')->default(true);
            $table->integer('limit_per_guest')->nullable();
            $table->integer('display_order')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'drink_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_drinks');
    }
};
