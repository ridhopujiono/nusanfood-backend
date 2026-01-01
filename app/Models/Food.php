<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'foods';
    protected $fillable = [
        'name',
        'food_type',
        'description',
        'source',
    ];

    public function servings()
    {
        return $this->hasMany(FoodServing::class);
    }
}
