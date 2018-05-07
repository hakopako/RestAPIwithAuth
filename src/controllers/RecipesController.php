<?php

namespace App\Controllers;

use App\Models\Recipe;
use App\Models\Rating;
use App\Middlewares\AuthMiddleware as Auth;

class RecipesController extends Controller {

    public function list($request)
    {
        $recipes = Recipe::allValid();
        return $this->response($recipes);
    }

    public function create($request)
    {
        if(!Auth::check($request)) {
            return $this->response([], 403, "Authentication Required");
        }
        if(!isset($request['data']->name) ||
           !isset($request['data']->prep_time) ||
           !isset($request['data']->difficulty) ||
           !isset($request['data']->is_vegetarian))
        {
            return $this->response([], 400, "Missing Requied Field(s).");
        }

        if(!preg_match("/^\w{8,100}$/", $request['data']->name) ||
           !preg_match("/^\d{1,3}$/", $request['data']->prep_time) ||
           !preg_match("/^[123]$/", $request['data']->difficulty) ||
           !preg_match("/^[tf]$/", $request['data']->is_vegetarian))
        {
            return $this->response([], 400, "Invalid Format.");
        }

        $recipe = new Recipe([
            'name' => $request['data']->name,
            'prep_time' => $request['data']->prep_time,
            'difficulty' => $request['data']->difficulty,
            'is_vegetarian' => $request['data']->is_vegetarian,
        ]);
        if($recipe->save()){
            return $this->response([]);
        } else {
            return $this->response([], 400, "Failed to create new record.");
        }
    }

    public function update($request)
    {
        if(!Auth::check($request)) {
            return $this->response([], 403, "Authentication Required");
        }
        $recipe = Recipe::findValid($request['id']);
        if(!$recipe){
            return $this->response([], 400, "Not Exists");
        }
        $recipe->is_vegetarian = ($recipe->is_vegetarian == '') ? 'f' : 't';
        $recipe->is_valid = ($recipe->is_valid == '') ? 'f' : 't';
        foreach ((array)$request['data'] as $key => $value) {
            if(($key == 'name' and preg_match("/^\w{8,100}$/", $value)) ||
              ($key == 'prep_time' and preg_match("/^\d{1,3}$/", $value)) ||
              ($key == 'difficulty' and preg_match("/^[123]$/", $value)) ||
              ($key == 'is_vegetarian' and preg_match("/^[tf]$/", $value)))
            {
                $recipe->{$key} = $value;
            } else {
                return $this->response([], 400, "Invalid Format.");
            }
        }
        $recipe->updated_at = (new \Datetime)->format('Y-m-d H:i:s');
        if($recipe->save()){
            return $this->response([]);
        } else {
            return $this->response([], 400, "Failed to update the record.");
        }
    }

    public function show($request)
    {
        $recipe = Recipe::findValid($request['id']);
        if(!$recipe){
            return $this->response([], 400, "Not Exists");
        }
        return $this->response($recipe->toArray());
    }

    public function destory($request)
    {
        if(!Auth::check($request)) {
            return $this->response([], 403, "Authentication Required");
        }
        // Logical deletion
        $recipe = Recipe::findValid($request['id']);
        if(!$recipe){
            return $this->response([], 400, "Not Exists");
        }
        $recipe->is_vegetarian = ($recipe->is_vegetarian == '') ? 'f' : 't';
        $recipe->is_valid = 'f';
        $recipe->updated_at = (new \Datetime)->format('Y-m-d H:i:s');
        if($recipe->save()){
            return $this->response([]);
        } else {
            return $this->response([], 400, "Failed to delete the record.");
        }
    }

    public function search($request)
    {
        if(!isset($request['params']['q'])){
            return $this->response([], 400, "Query 'q' not found.");
        }
        if(!preg_match("/^\w+$/", $request['params']['q'])){
            return $this->response([], 400, "Invalid Keyword.");
        }
        $keyword = $request['params']['q'];
        $recipes = Recipe::where("name LIKE '%$keyword%' AND is_valid is true");
        return $this->response($recipes);
    }

    public function rating($request)
    {
        $recipe = Recipe::findValid($request['id']);
        if(!$recipe){
            return $this->response([], 400, "Not Exists");
        }
        if(!isset($request['data']->score)) {
            return $this->response([], 400, "Missing Requied Field(s).");
        }
        if(!preg_match("/^[12345]$/", $request['data']->score)) {
            return $this->response([], 400, "Invalid Format.");
        }

        $rating = new Rating([
            'recipe_id' => $recipe->id,
            'score' => $request['data']->score,
        ]);
        if($rating->save()){
            return $this->response([]);
        } else {
            return $this->response([], 400, "Failed to create new record.");
        }
    }

}
