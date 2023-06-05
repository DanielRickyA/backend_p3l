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
Route::get('PromoAll', 'App\Http\Controllers\Api\PromoController@tampilPromo');
Route::get('JadwalHarianAll', 'App\Http\Controllers\Api\JadwalHarianController@getjadwalHarian');

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

    Route::get('PresensiBookingGymToday', 'App\Http\Controllers\Api\PresensiBookingGymController@getPresensiBookingGymToday');
    Route::get('PresensiBookingGym/{id}', 'App\Http\Controllers\Api\PresensiBookingGymController@getPresensiBookingGymById');
    Route::patch('PresensiBookingGymToday/{id}', 'App\Http\Controllers\Api\PresensiBookingGymController@PresensiMember');

    Route::get('PresensiBookingKelasToday' , 'App\Http\Controllers\Api\PresensiBookingKelasController@getPresensiKelasToday');
    Route::get('PresensiBookingKelas/{id}' , 'App\Http\Controllers\Api\PresensiBookingKelasController@getPresnesiBookingKelasById');
    

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

    // Moobile
    Route::get('getJadwalHarianToday', 'App\Http\Controllers\Api\JadwalHarianController@getjadwalHarianToday');
    Route::get('PresensiInstruktur', 'App\Http\Controllers\Api\PresensiInstrukturController@getPresensiToday');
    Route::get('PresensiInstrukturToday', 'App\Http\Controllers\Api\PresensiInstrukturController@getPresensiAllToday');
    Route::post('PresensiInstruktur', 'App\Http\Controllers\Api\PresensiInstrukturController@SetPresensInstruktur');
    Route::patch('PresensiInstruktur/{id}', 'App\Http\Controllers\Api\PresensiInstrukturController@setJamSelesaiPresensi');

    Route::get('LaporanGym/{month}', 'App\Http\Controllers\Api\LaporanPerbulanController@getGymActivityMonthly');
    Route::get('LaporanKelas/{month}', 'App\Http\Controllers\Api\LaporanPerbulanController@getKelasActivityMonthly');
    Route::get('LaporanDana', 'App\Http\Controllers\Api\LaporanPerbulanController@getMonthlyIncome');
    Route::get('LaporanKinerjaIns/{month}', 'App\Http\Controllers\Api\LaporanPerbulanController@getKinerjaInstrukturMonthly');


    Route::post('LogoutPegawai', 'App\Http\Controllers\Api\AuthController@LogoutPegawai');
});

Route::group(['middleware' => 'auth:instrukturP'], function () {
    // Instruktur
    Route::get('showInstrukturHarian', 'App\Http\Controllers\Api\IjinInstrukturController@showJadwalInsturktur');
    Route::get('showIjinInsturktur', 'App\Http\Controllers\Api\IjinInstrukturController@showIzinInsturktur');
    Route::post('PerizinanInstruktur', 'App\Http\Controllers\Api\IjinInstrukturController@requestIzin');

    Route::get('Instruktur/{id}', 'App\Http\Controllers\Api\InstrukturController@show');
    Route::get('cekHistoryKelasInstruktur', 'App\Http\Controllers\Api\PresensiInstrukturController@getPresensiInstruktur');
    // 
    
});

Route::group(['middleware' => 'auth:memberP'], function () {
    Route::get('JadwalHarianM', 'App\Http\Controllers\Api\JadwalHarianController@getjadwalHarianM');
    Route::post('PresensiBookingKelas', 'App\Http\Controllers\Api\PresensiBookingKelasController@store');
 
    Route::get('BookingGym', 'App\Http\Controllers\Api\PresensiBookingGymController@showBookingGymMember');
    Route::post('BookingGym', 'App\Http\Controllers\Api\PresensiBookingGymController@bookingGym');
    Route::delete('BookingGym/{id}', 'App\Http\Controllers\Api\PresensiBookingGymController@batalKelas');

    Route::get('Member/{id}', 'App\Http\Controllers\Api\MemberController@show');

    Route::get('cekDepositM/{id}', 'App\Http\Controllers\Api\DepositKelasController@showDepoKelasMember');
    Route::get('cekHistoryKelas/{id}' , 'App\Http\Controllers\Api\PresensiBookingKelasController@getHistoryBookingM');
});