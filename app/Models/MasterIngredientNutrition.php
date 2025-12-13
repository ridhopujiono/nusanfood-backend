<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterIngredientNutrition extends Model
{
    // food_name
    // food_unit

    protected $table = 'master_ingredient_nutritions';

    protected $fillable = [
        'food_name_translated',
        'food_unit',
        'is_verified',
    ];
}
