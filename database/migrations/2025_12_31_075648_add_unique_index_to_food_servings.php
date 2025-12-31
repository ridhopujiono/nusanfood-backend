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
        Schema::table('food_servings', function (Blueprint $table) {
            $table->unique([
                'food_id',
                'serving_label',
                'metric_amount',
                'metric_unit'
            ], 'food_servings_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_servings', function (Blueprint $table) {
            $table->dropUnique('food_servings_unique');
        });
    }
};
