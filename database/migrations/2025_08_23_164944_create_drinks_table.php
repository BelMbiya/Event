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
        Schema::create('drinks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->enum('type', ['water','soft','juice','hot','beer','wine','spirit','cocktail','other']);
            $table->boolean('alcoholic')->default(false);
            $table->enum('unit', ['glass','bottle','can','cup','other']);
            $table->integer('volume_ml')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drinks');
    }
};
