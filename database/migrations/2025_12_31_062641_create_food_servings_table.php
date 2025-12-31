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
        Schema::create('food_servings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_id');
            $table->string('serving_label', 100); // "100 g", "1 cup"

            $table->decimal('metric_amount', 10, 3)->nullable();
            $table->string('metric_unit', 20)->nullable(); // g, ml

            $table->decimal('household_amount', 10, 3)->nullable();
            $table->string('household_unit', 50)->nullable(); // cup, tbsp

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_servings');
    }
};
