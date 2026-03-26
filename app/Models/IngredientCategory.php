<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
    ];

    // このカテゴリに属する食材
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'ingredient_cat_id');
    }
}
