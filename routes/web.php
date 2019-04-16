<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return "Welcome to API Digital ID";
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('did', ['uses' => 'DigitalIdController@postData']);

    $router->get('did/{id}', ['uses' => 'DigitalIdController@showDid']);
    $router->get('test', ['uses' => 'DigitalIdController@test']);
    $router->get('did', ['uses' => 'DigitalIdController@root']);


    $router->get('authors', ['uses' => 'AuthorController@showAllAuthors']);
    $router->get('authors/{id}', ['uses' => 'AuthorController@showOneAuthor']);
    $router->post('authors', ['uses' => 'AuthorController@create']);
    $router->delete('authors/{id}', ['uses' => 'AuthorController@delete']);

    $router->put('authors/{id}', ['uses' => 'AuthorController@update']);
});