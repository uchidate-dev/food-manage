<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // このカテゴリに属するレシピ
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'recipe_category_id');
    }
}
