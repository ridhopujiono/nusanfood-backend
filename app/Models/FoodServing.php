<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodServing extends Model
{
    protected $fillable = [
        'food_id',
        'serving_label',
        'metric_amount',
        'metric_unit',
        'household_amount',
        'household_unit',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function nutrition()
    {
        return $this->hasOne(NutritionFact::class, 'serving_id');
    }
}
