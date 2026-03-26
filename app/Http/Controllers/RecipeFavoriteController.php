<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeFavoriteController extends Controller
{
    public function toggle(Request $request, Recipe $recipe)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $exists = $user->favoriteRecipes()
            ->where('recipes.id', $recipe->id)
            ->exists();

        if ($exists) {
            $user->favoriteRecipes()->detach($recipe->id);
            $favoriteFlg = 0;
        } else {
            $user->favoriteRecipes()->attach($recipe->id);
            $favoriteFlg = 1;
        }

        return response()->json([
            'favorite_flg' => $favoriteFlg,
        ]);
    }
}