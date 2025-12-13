<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterIngredientNutrition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);
        $search = $request->query('search');
        return MasterIngredientNutrition::orderBy('food_name_translated')
            ->when($search, function ($query) use ($search) {
                return $query
                    ->where('food_name_translated', 'like', "%{$search}%")
                    ->orWhere('food_unit', 'like', "%{$search}%");
            })
            ->paginate($perPage);
    }

    public function show($id)
    {
        $rows = DB::table('master_ingredient_nutritions as min2')
            ->join(
                'master_ingredient_nutrition_details as mind',
                'mind.master_ingredient_nutritions_id',
                '=',
                'min2.id'
            )
            ->join(
                'master_nutritions as mn',
                'mind.attr_id',
                '=',
                'mn.attr_id'
            )
            ->where('min2.id', $id)
            ->select(
                'min2.id',
                'min2.food_name_translated as food_name',
                'min2.food_unit',
                'mn.nutrition_name',
                'mind.value',
                'mn.unit'
            )
            ->orderBy('mn.nutrition_name')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'id' => $rows[0]->id,
            'food_name' => $rows[0]->food_name,
            'food_unit' => $rows[0]->food_unit,
            'nutritions' => $rows->map(fn($r) => [
                'nutrition_name' => $r->nutrition_name,
                'value' => $r->value,
                'unit' => $r->unit,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'food_name_translated' => 'required|string',
            'food_unit' => 'required|string',
        ]);

        return MasterIngredientNutrition::create($data);
    }

    public function update(Request $request, MasterIngredientNutrition $food)
    {
        $data = $request->validate([
            'food_name_translated' => 'required|string',
            'food_unit' => 'required|string',
        ]);

        $food->update($data);

        return $food;
    }

    public function verify($id)
    {
        $food = MasterIngredientNutrition::findOrFail($id);

        $food->update([
            'is_verified' => ! $food->is_verified,
        ]);

        return response()->json([
            'id' => $food->id,
            'is_verified' => $food->is_verified,
        ]);
    }


    public function destroy(MasterIngredientNutrition $food)
    {
        $food->delete();

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}
