<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipe_category_id',
        'title',
        'url',
        'private_flg',
        'favorite_flg',
        'ingredients',
        'memo',
        'kids_tips',
        'steps',
        'nutrition',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'recipe_category_id' => 'integer',
        'private_flg' => 'integer',
        'favorite_flg' => 'integer',
        'steps' => 'array',
        'nutrition' => 'array',
    ];

    // 作成者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリ
    public function category()
    {
        return $this->belongsTo(RecipeCategory::class, 'recipe_category_id');
    }

    // このレシピをお気に入りしているユーザー（多対多）
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'recipe_favorites')
            ->withTimestamps();
    }

    // このレシピの閲覧履歴（ログ）
    public function views()
    {
        return $this->hasMany(RecipeView::class);
    }
}
