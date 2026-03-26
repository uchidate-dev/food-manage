<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'mail_flg',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // リレーション

    // ユーザーが持つ食材
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    // ユーザーが投稿したレシピ
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    // ユーザーがお気に入りしたレシピ（多対多）
    public function favoriteRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_favorites')
            ->withTimestamps();
    }

    // ユーザーのレシピ閲覧履歴（ログ）
    public function recipeViews()
    {
        return $this->hasMany(RecipeView::class);
    }
}