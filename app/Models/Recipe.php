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
        'finished_image',
        'memo',
        'step1','step2','step3','step4','step5',
        'step6','step7','step8','step9','step10',
        'step11','step12','step13','step14','step15',
        'step_image1','step_image2','step_image3','step_image4','step_image5',
        'step_image6','step_image7','step_image8','step_image9','step_image10',
        'step_image11','step_image12','step_image13','step_image14','step_image15',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'recipe_category_id' => 'integer',
        'private_flg' => 'integer',
        'favorite_flg' => 'integer',
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