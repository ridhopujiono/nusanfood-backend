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
        // Perpanjang kolom teks agar fleksibel menerima variasi label dan satuan.
        Schema::table('food_servings', function (Blueprint $table) {
            $table->string('serving_label')->change(); // "100 g", "1 cup"
            $table->string('metric_unit', 50)->nullable()->change(); // g, ml
            $table->string('household_unit')->nullable()->change(); // cup, tbsp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan panjang kolom ke batas semula.
        Schema::table('food_servings', function (Blueprint $table) {
            $table->string('serving_label', 100)->change();
            $table->string('metric_unit', 20)->nullable()->change();
            $table->string('household_unit', 50)->nullable()->change();
        });
    }
};
