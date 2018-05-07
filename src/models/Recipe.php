<?php

namespace App\Models;

class Recipe extends Model {

    static protected $table = 'recipes';

    static public function findValid($id)
    {
        $recipe = Recipe::find($id);
        return ($recipe != null and $recipe->is_valid == 't') ? $recipe : null;
    }

    static public function allValid()
    {
        return Recipe::where("is_valid is true");
    }

}
