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
Route::put('ChangePasswordInsturktur', 'App\Http\Controllers\Api\AuthController@ChangePasswordInsturktur');
Route::put('ChangePasswordMO', 'App\Http\Controllers\Api\AuthController@ChangePasswordPegawai');


Route::group(['middleware' => 'auth:pegawaiP'], function () {
    // Kasir
    Route::get('Member', 'App\Http\Controllers\Api\MemberController@index');
    Route::get('Member/{id}', 'App\Http\Controllers\Api\MemberController@show');
    Route::post('Member', 'App\Http\Controllers\Api\MemberController@store');
    Route::put('Member/{id}', 'App\Http\Controllers\Api\MemberController@update');
    Route::delete('Member/{id}', 'App\Http\Controllers\Api\MemberController@destroy');
    Route::patch('Member/{id}', 'App\Http\Controllers\Api\MemberController@resetPassword');

    Route::get('TransaksiAktivasi', 'App\Http\Controllers\Api\TransaksiAktivasiController@getAllAktivasi');
    Route::get('TransaksiAktivasi/{id}', 'App\Http\Controllers\Api\TransaksiAktivasiController@getByIdAktivasi');
    Route::post('TransaksiAktivasi', 'App\Http\Controllers\Api\TransaksiAktivasiController@transaksiAktivasi');
    Route::patch('TransaksiDeaktivasi/{id}', 'App\Http\Controllers\Api\TransaksiAktivasiController@deactivasiMember');

    Route::get('TransaksiDepositUang', 'App\Http\Controllers\Api\TransaksiDepoUangController@getAllDepositUang');
    Route::get('TransaksiDepositUang/{id}', 'App\Http\Controllers\Api\TransaksiDepoUangController@getByIdDU');
    Route::post('TransaksiDepositUang', 'App\Http\Controllers\Api\TransaksiDepoUangController@transaksiDepositUang');

    Route::get('TransaksiDepositKelas', 'App\Http\Controllers\Api\TransaksiDepoKelasController@getAllDepositKelas');
    Route::get('TransaksiDepositKelas/{id}', 'App\Http\Controllers\Api\TransaksiDepoKelasController@getByIdDK');
    Route::post('TransaksiDepositKelas', 'App\Http\Controllers\Api\TransaksiDepoKelasController@transaksiDepositKelas');
    

    // Admin
    Route::get('Instruktur', 'App\Http\Controllers\Api\InstrukturController@index');
    Route::get('Instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@show');
    Route::post('Instruktur', 'App\Http\Controllers\Api\InstrukturController@store');
    Route::put('Instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@update');
    Route::delete('Instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@destroy');

    // MO

    Route::get('JadwalUmum', 'App\Http\Controllers\Api\JadwalUmumController@index');
    Route::get('JadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@show');
    Route::post('JadwalUmum', 'App\Http\Controllers\Api\JadwalUmumController@store');
    Route::put('JadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@update');
    Route::delete('JadwalUmum/{id}', 'App\Http\Controllers\Api\JadwalUmumController@destroy');

    Route::get('JadwalHarian', 'App\Http\Controllers\Api\JadwalHarianController@index');
    Route::post('JadwalHarian', 'App\Http\Controllers\Api\JadwalHarianController@generateJadwalHarian');
    Route::patch('JadwalHarian/{id}', 'App\Http\Controllers\Api\JadwalHarianController@changeStatus');

    Route::get('PerizinanInstruktur', 'App\Http\Controllers\Api\IjinInstrukturController@index');
    Route::put('PerizinanInstrukturK/{id}', 'App\Http\Controllers\Api\IjinInstrukturController@konfirmPerizinan');
    Route::put('PerizinanInstrukturT/{id}', 'App\Http\Controllers\Api\IjinInstrukturController@tolakPerizinan');
    

    
    Route::get('Kelas', 'App\Http\Controllers\Api\KelasController@index');
    Route::get('Promo', 'App\Http\Controllers\Api\PromoController@index');

    Route::post('LogoutPegawai', 'App\Http\Controllers\Api\AuthController@LogoutPegawai');
});

Route::group(['middleware' => 'auth:instrukturP'], function () {
    // Instruktur
    Route::get('showInstrukturHarian', 'App\Http\Controllers\Api\IjinInstrukturController@showJadwalInsturktur');
    Route::get('showIjinInsturktur', 'App\Http\Controllers\Api\IjinInstrukturController@showIzinInsturktur');
    Route::post('PerizinanInstruktur', 'App\Http\Controllers\Api\IjinInstrukturController@requestIzin');

    // 
    Route::get('JadwalHarianM', 'App\Http\Controllers\Api\JadwalHarianController@getjadwalHarianM');
 

    

});
  