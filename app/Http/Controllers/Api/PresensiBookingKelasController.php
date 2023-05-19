<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositKelas;
use App\Models\JadwalHarian;
use App\Models\Member;
// use App\Models\PresensiBookingKelas;
use App\Models\PresensiBookingKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PresensiBookingKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $presensiBookingKelas = PresensiBookingKelas::with(['FMember', 'FJadwalHarian'])->get();
        return response([
            'message' => 'Berhasil menerima data',
            'data' => $presensiBookingKelas
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_member' => 'required',
            // making id_insturktur, hari_kelas, and jam_kelas unique in the same time
            'tanggal_jadwal_harian' => 'required',
            'tarif' => 'required',
        ]);
        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }

        $member = Member::where('id', $request->id_member)->first();
        $jadwalHarian = JadwalHarian::with(['FKelas', 'FPegawai'])->where('tanggal_jadwal_harian', $request->id_jadwal_harian)->first();
        // cek member aktif
        if ($member->status == 'Inactive') {
            return response([
                'message' => 'Member Tidak Aktif',
            ], 400);
        }

        // cek jadwal harian
        if($jadwalHarian->status == 'Inactive'){
            return response([
                'message' => 'Jadwal Harian Tidak Aktif',
            ], 400);
        }

        $sisaDeposit = DepositKelas::   where([
            'id_member' => $member['id'],
            'id_kelas' => $jadwalHarian['id_kelas'],
        ])->first();
        // cek kouta kelas
        $cekPenuh = $jadwalHarian['fkelas']['id']->count();
        if($cekPenuh == 0){
            return response([
                'message' => 'Kelas Penuh',
            ], 400);
        }
        // Cek Tarif jika deposit kelas
        if($storeData['tarif'] == 0){
            if ($sisaDeposit['sisa_deposit'] <= 0) {
                return response([
                    'message' => 'Sisa Deposit Tidak Mencukupi',
                ], 400);
            }else{  
                $sisaDeposit['sisa_deposit'] = $sisaDeposit['sisa_deposit'] - 1;
            }
        // Cek Tarif jika bukan deposit uang
        }else{
            if($member['deposit_uang'] < $jadwalHarian['fkelas']['id']){
                return response([
                    'message' => 'Deposit Uang Tidak Mencukupi',
                ], 400);
            }else{
                $member['deposit_uang'] = $member['deposit_uang'] - $jadwalHarian['fkelas']['id'];
            }
        }

        $storeData['tanggal_booking'] = date('Y-m-d');
        $storeData['tanggal_yang_dibooking'] = $jadwalHarian['tanggal_jadwal_harian'];
        $presensiBookingkelas = PresensiBookingKelas::create($storeData);

        return response([
            'message' => 'Berhasil Menambahkan Data',
            'data' => $presensiBookingkelas,

        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $PBKelas = PresensiBookingKelas::with(['FMember', 'FJadwalHarian'])->find($id);

        if (is_null($PBKelas)) {
            return response([
                'message' => 'Data Tidak ada',
                'data' => null
            ], 200);
        }

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
