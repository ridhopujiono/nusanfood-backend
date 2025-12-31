<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionFact extends Model
{
    protected $fillable = [
        'serving_id',
        'calories',
        'carbohydrate',
        'protein',
        'fat',
        'saturated_fat',
        'polyunsaturated_fat',
        'monounsaturated_fat',
        'fiber',
        'sugar',
        'cholesterol',
        'sodium',
        'potassium',
        'vitamin_a',
        'vitamin_c',
        'calcium',
        'iron',
    ];

    public function serving()
    {
        return $this->belongsTo(FoodServing::class, 'serving_id');
    }
}
