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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            $table->foreignId('guest_type_id')->nullable()->constrained('guest_types')->nullOnDelete();
            $table->foreignId('event_table_id')->nullable()->constrained('event_tables')->nullOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('rsvp_status', ['pending','confirmed','declined'])->default('pending');
            $table->string('meal_choice')->nullable();
            $table->string('unique_url')->unique()->nullable();
            $table->timestamp('response_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
