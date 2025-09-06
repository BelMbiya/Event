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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->nullable()->constrained('invitations')->onDelete('cascade');
            $table->string("couple")->nullable();
            $table->string("Epoux")->nullable();
            $table->string("Epouse")->nullable();
            $table->string("Famille_epoux")->nullable();
            $table->string("Famille_epouse")->nullable();
            $table->text("Content")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
