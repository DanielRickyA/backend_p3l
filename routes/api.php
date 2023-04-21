<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('loginPegawai', 'App\Http\Controllers\Api\AuthController@loginPegawai');
Route::post('loginInstruktur', 'App\Http\Controllers\Api\AuthController@loginInstruktur');
Route::post('loginMember', 'App\Http\Controllers\Api\AuthMemberController@loginMember');


// Route::group(['middleware' => 'auth:api'], function(){
    Route::get('Member', 'App\Http\Controllers\Api\MemberController@index');
    Route::get('Member/{id}', 'App\Http\Controllers\Api\MemberController@show');
    Route::post('Member', 'App\Http\Controllers\Api\MemberController@store');
    Route::put('Member/{id}', 'App\Http\Controllers\Api\MemberController@update');
    Route::delete('Member/{id}', 'App\Http\Controllers\Api\MemberController@destroy');

    Route::get('Instruktur', 'App\Http\Controllers\Api\InstrukturController@index');
    Route::get('Instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@show');
    Route::post('Instruktur', 'App\Http\Controllers\Api\InstrukturController@store');
    Route::put('Instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@update');
    Route::delete('Instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@destroy');

    Route::get('JadwalUmum', 'App\Http\Controllers\Api\JadwalUmumController@index');
    Route::get('JadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@show');
    Route::post('JadwalUmum', 'App\Http\Controllers\Api\JadwalUmumController@store');
    Route::put('JadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@update');
    Route::delete('JadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@destroy');
// });


