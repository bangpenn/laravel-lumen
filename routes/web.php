<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return $router->app->version();
});

$router->get('/posts', 'PostsController@index');
$router->post('/posts', 'PostsController@store');
$router->get('/posts/{id}', 'PostsController@show');
$router->put('/posts/{id}', 'PostsController@update');
$router->delete('/posts/{id}', 'PostsController@destroy');

// Jenis-aspek
Route::group(['prefix' => 'jenis-aspek'], function () {
    Route::get('/index', 'JenisAspekController@GetJenisAspek');
    Route::post('/createorupdate', 'JenisAspekController@CreateOrUpdateJenisAspek');
    Route::delete('/{id}/delete', 'JenisAspekController@DestroyJenisAspek');
});

// Bobot-akademik
Route::group(['prefix' => 'bobot-akademik'], function () {
    Route::get('/index', 'BobotAkademikController@GetBobotAkademik');
    Route::post('/createorupdate', 'BobotAkademikController@CreateOrUpdateBobotAkademik');
    Route::delete('/{id}/delete', 'BobotAkademikController@DestroyBobotAkademik');
});

// Nilai-akademik
Route::group(['prefix' => 'nilai-akademik'], function () {
    Route::get('/index', 'NilaiAkademikController@GetNilaiAkademik');
    Route::post('/createorupdate', 'NilaiAkademikController@CreateOrUpdateNilaiAkademik');
    Route::delete('{id}/delete', 'NilaiAkademikController@DestroyNilaiAkademik');
});
