<?php

/**
 * TODO: Cambiar registration_type_id a registration_type como string
 * TODO: Configurar external Request para cada Company
 */

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

Route::namespace('Api')->prefix('v2')->middleware('auth:api')->group(function () {
    Route::get('registrations', 'RegistrationController@index');
    Route::post('registrations', 'RegistrationController@store');
    Route::get('registrations/{id}', 'RegistrationController@show');
    Route::delete('registrations/{id}', 'RegistrationController@destroy');

    Route::post('authorizations/{id}', 'AuthorizationController@update');

    Route::post('verifications', 'VerificationController@store');

    Route::get('products', 'ProductController@index');
    Route::post('products', 'ProductController@store');

    Route::get('users/{id}', 'UserController@show');
});
