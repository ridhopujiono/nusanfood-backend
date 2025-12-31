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
        Schema::create('nutrition_facts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serving_id');

            $table->decimal('calories', 10, 2)->nullable();
            $table->decimal('carbohydrate', 10, 2)->nullable();
            $table->decimal('protein', 10, 2)->nullable();
            $table->decimal('fat', 10, 2)->nullable();

            $table->decimal('saturated_fat', 10, 2)->nullable();
            $table->decimal('polyunsaturated_fat', 10, 2)->nullable();
            $table->decimal('monounsaturated_fat', 10, 2)->nullable();

            $table->decimal('fiber', 10, 2)->nullable();
            $table->decimal('sugar', 10, 2)->nullable();

            $table->decimal('cholesterol', 10, 2)->nullable();
            $table->decimal('sodium', 10, 2)->nullable();
            $table->decimal('potassium', 10, 2)->nullable();

            $table->decimal('vitamin_a', 10, 2)->nullable();
            $table->decimal('vitamin_c', 10, 2)->nullable();
            $table->decimal('calcium', 10, 2)->nullable();
            $table->decimal('iron', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_facts');
    }
};
