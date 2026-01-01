<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ImportFoodFromFatSecretJob;
use App\Models\Food;
use App\Models\FoodServing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Bus;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FoodController extends Controller
{
    /**
     * LIST FOOD
     */
    public function index(Request $request)
    {
        return Food::with('servings.nutrition')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);
    }

    /**
     * CREATE FOOD (nama bahan)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:foods,name',
        ]);


        ImportFoodFromFatSecretJob::dispatchSync($request->name);

        return response()->json([
            'data' => $request->all()
        ]);
    }

    /**
     * SHOW FOOD + SERVINGS + NUTRITION
     */
    public function show(Food $food)
    {
        return $food->load('servings.nutrition');
    }

    /**
     * UPDATE FOOD (nama bahan)
     */
    public function update(Request $request, Food $food)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('foods', 'name')->ignore($food->id),
            ],
            'food_type' => 'nullable|string|max:50',
        ]);

        $food->update($data);

        return $food;
    }

    /**
     * DELETE FOOD (cascade ke serving + nutrition)
     */
    public function destroy(Food $food)
    {
        $food->delete();

        return response()->noContent();
    }

    /**
     * UPDATE NUTRITION (BERDASARKAN SERVING)
     *
     * UI flow:
     * - user pilih food
     * - pilih serving (dropdown)
     * - edit nilai nutrisi
     */
    public function updateNutrition(
        Request $request,
        Food $food,
        FoodServing $serving
    ) {
        // pastikan serving milik food
        abort_if($serving->food_id !== $food->id, 404);

        $data = $request->validate([
            'calories' => 'nullable|numeric',
            'carbohydrate' => 'nullable|numeric',
            'protein' => 'nullable|numeric',
            'fat' => 'nullable|numeric',

            'saturated_fat' => 'nullable|numeric',
            'polyunsaturated_fat' => 'nullable|numeric',
            'monounsaturated_fat' => 'nullable|numeric',

            'fiber' => 'nullable|numeric',
            'sugar' => 'nullable|numeric',

            'cholesterol' => 'nullable|numeric',
            'sodium' => 'nullable|numeric',
            'potassium' => 'nullable|numeric',

            'vitamin_a' => 'nullable|numeric',
            'vitamin_c' => 'nullable|numeric',
            'calcium' => 'nullable|numeric',
            'iron' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($serving, $data) {
            $serving->nutrition()->updateOrCreate([], $data);
        });

        return $serving->load('nutrition');
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $path = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // asumsi:
        // kolom A = food_name / search_expression
        $jobs = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // header
            if (empty($row[0])) continue;

            $jobs[] = new ImportFoodFromFatSecretJob(trim($row[0]));
        }

        $batch = Bus::batch($jobs)
            ->name('Import Food From Excel')
            ->dispatch();

        return response()->json([
            'batch_id' => $batch->id,
            'total_jobs' => count($jobs),
        ]);
    }
}
