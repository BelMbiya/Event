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
        Schema::table('contents', function (Blueprint $table) {
            // ✅ SUPPRESSION : Colonnes obsolètes qui ne sont plus utilisées
            if (Schema::hasColumn('contents', 'Epoux')) {
                $table->dropColumn('Epoux');
            }
            if (Schema::hasColumn('contents', 'Epouse')) {
                $table->dropColumn('Epouse');
            }
            if (Schema::hasColumn('contents', 'Famille_epoux')) {
                $table->dropColumn('Famille_epoux');
            }
            if (Schema::hasColumn('contents', 'Famille_epouse')) {
                $table->dropColumn('Famille_epouse');
            }
            if (Schema::hasColumn('contents', 'Content')) {
                $table->dropColumn('Content');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Restaurer les colonnes obsolètes si nécessaire
            $table->string("Epoux")->nullable();
            $table->string("Epouse")->nullable();
            $table->string("Famille_epoux")->nullable();
            $table->string("Famille_epouse")->nullable();
            $table->text("Content")->nullable();
        });
    }
};
