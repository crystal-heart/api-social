<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('auth/register', 'ApiController@register');
Route::post('auth/login', 'ApiController@login');
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('user-info', 'ApiController@myProfile');
    Route::get('/stories', 'ApiController@myStories');
    Route::post('story/create', 'ApiController@createStory');
    Route::get('story/show/{id}', 'ApiController@showStory');
    Route::post('story/update', 'ApiController@updateStory');
    Route::get('story/delete/{id}', 'ApiController@deleteStory');
    Route::post('synData', 'ApiController@synData');
});
