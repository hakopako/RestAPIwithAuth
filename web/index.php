<?php

require __DIR__.'/../vendor/autoload.php';

// display error reports for development
ini_set('display_errors', 1);
error_reporting(E_ALL);

use \App\Route;

$route = new Route();

// ------------------------
// Auth
// ------------------------
$route->add('POST', '/login', 'AuthController@login');

// ------------------------
// Recipe
// ------------------------
$route->add('GET', '/recipes', 'RecipesController@list');
$route->add('POST', '/recipes', 'RecipesController@create');
$route->add('GET', '/recipes/{id}', 'RecipesController@show', ['id' => "\d{1,10}"]);
$route->add('PUT', '/recipes/{id}', 'RecipesController@update', ['id' => "\d{1,10}"]);
$route->add('DELETE', '/recipes/{id}', 'RecipesController@destory', ['id' => "\d{1,10}"]);
$route->add('POST', '/recipes/{id}/rating', 'RecipesController@rating', ['id' => "\d{1,10}"]);
$route->add('GET', '/recipes/search', 'RecipesController@search');

$route->run();
