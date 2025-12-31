<?php

namespace App\Jobs;

use App\Models\Food;
use App\Services\FatSecretService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportFoodFromFatSecretJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $searchExpression
    ) {}

    public function handle(FatSecretService $fatSecret)
    {
        /**
         * STEP 1: Search food
         */
        $searchResult = $fatSecret->searchFood($this->searchExpression);

        if (!isset($searchResult['foods']['food']['food_id'])) {
            Log::info('No food found');
            return;
        }

        $foodId = $searchResult['foods']['food']['food_id'];

        /**
         * STEP 2: Get food detail
         */
        $foodDetail = $fatSecret->getFoodDetail($foodId);

        if (!isset($foodDetail['food'])) {
            Log::info('No food detail found');
            return;
        }

        $foodData = $foodDetail['food'];

        /**
         * STEP 3: Save FOOD (tanpa fatsecret id)
         */
        $food = Food::firstOrCreate(
            [
                'name' => $foodData['food_name'],
            ],
            [
                'food_type' => $foodData['food_type'] ?? null,
                'source' => 'fatsecret',
            ]
        );

        /**
         * STEP 4: Save SERVINGS + NUTRITION
         */
        $servings = $foodData['servings']['serving'] ?? [];

        foreach ($servings as $serving) {

            $foodServing = $food->servings()->updateOrCreate(
                [
                    'serving_label' => $serving['serving_description'],
                    'metric_amount' => $serving['metric_serving_amount'] ?? null,
                    'metric_unit' => $serving['metric_serving_unit'] ?? null,
                ],
                [
                    'household_amount' => $serving['number_of_units'] ?? null,
                    'household_unit' => $serving['measurement_description'] ?? null,
                ]
            );

            $foodServing->nutrition()->updateOrCreate(
                [],
                [
                    'calories' => $serving['calories'] ?? null,
                    'carbohydrate' => $serving['carbohydrate'] ?? null,
                    'protein' => $serving['protein'] ?? null,
                    'fat' => $serving['fat'] ?? null,

                    'saturated_fat' => $serving['saturated_fat'] ?? null,
                    'polyunsaturated_fat' => $serving['polyunsaturated_fat'] ?? null,
                    'monounsaturated_fat' => $serving['monounsaturated_fat'] ?? null,

                    'fiber' => $serving['fiber'] ?? null,
                    'sugar' => $serving['sugar'] ?? null,

                    'cholesterol' => $serving['cholesterol'] ?? null,
                    'sodium' => $serving['sodium'] ?? null,
                    'potassium' => $serving['potassium'] ?? null,

                    'vitamin_a' => $serving['vitamin_a'] ?? null,
                    'vitamin_c' => $serving['vitamin_c'] ?? null,
                    'calcium' => $serving['calcium'] ?? null,
                    'iron' => $serving['iron'] ?? null,
                ]
            );
        }
    }
}
