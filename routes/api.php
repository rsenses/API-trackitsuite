<?php

use Illuminate\Http\Request;

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
    // Route::get('user', function (Request $request) {
    //     return $request->user();
    // });

    Route::get('registrations', 'RegistrationController@index');
    Route::post('registrations', 'RegistrationController@store');
    Route::get('registrations/{id}', 'RegistrationController@show');
    Route::delete('registrations/{id}', 'RegistrationController@destroy');

    Route::post('verifications', 'VerificationController@store');

    Route::post('products', 'ProductController@store');

    Route::get('users/{id}', 'UserController@show');
});
