<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ingredient_cat_id',
        'name',
        'quantity',
        'expiration_date',
        'storage_type',
        'delete_flg',
        'memo',
    ];

    public function user()
    { return $this->belongsTo(User::class); }

    public function category()
    { return $this->belongsTo(IngredientCategory::class,
    'ingredient_cat_id'); }

}
