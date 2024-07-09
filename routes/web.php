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
    Route::get('jenis-aspek', 'JenisAspekController@GetJenisAspek');
    Route::post('jenis-aspek/createorupdate', 'JenisAspekController@CreateOrUpdateJenisAspek');
    Route::delete('jenis-aspek/{id}', 'JenisAspekController@DestroyJenisAspek');
});

// Bobot-akademik
Route::group(['prefix' => 'bobot-akademik'], function () {
    Route::get('bobot-akademik', 'BobotAkademikController@GetJenisAspek');
    Route::post('bobot-akademik/createorupdate', 'BobotAkademikController@CreateOrUpdateBobotAkademik');
    Route::delete('bobot-akademik/{id}', 'BobotAkademikController@DestroyBobotAkademik');
});

// Nilai-akademik
Route::group(['prefix' => 'nilai-akademik'], function () {
    Route::get('nilai-akademik', 'NilaiAkademikController@GetNilaiAkademik');
    Route::post('nilai-akademik/createorupdate', 'NilaiAkademikController@CreateOrUpdateNilaiAkademik');
    Route::delete('nilai-akademik/{id}', 'NilaiAkademikController@DestroyNilaiAkademik');
});
