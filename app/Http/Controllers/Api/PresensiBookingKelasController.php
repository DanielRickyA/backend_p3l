<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositKelas;
use App\Models\JadwalHarian;
use App\Models\Member;
// use App\Models\PresensiBookingKelas;
use App\Models\PresensiBookingKelas;
use Haruncpi\LaravelIdGenerator\IdGenerator;
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
            'id_jadwal_harian' => 'required',
            'jenis_pembayaran' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $member = Member::where('id', $request->id_member)->first();
        $jadwalHarian = JadwalHarian::where('id', '=', $storeData['id_jadwal_harian'])->first();

        // cek member aktif
        if ($member->status == 'Inactive') {
            return response([
                'message' => 'Member Tidak Aktif',
            ], 400);
        }

        // cek jadwal harian
        if ($jadwalHarian->status == 'Inactive') {
            return response([
                'message' => 'Jadwal Harian Tidak Aktif',
            ], 400);
        }
        if(is_null($jadwalHarian)){
            return response([
                'message' => 'Jadwal Harian Tidak Ditemukan',
            ], 400);
        }

        $sisaDeposit = DepositKelas::where([
            'id_member' => $member['id'],
            'id_kelas' => $jadwalHarian['FJadwalUmum']['Fkelas']['id'],
        ])->first();

        
        // $cekPenuh = $jadwalHarian['tanggal_jadwal_harian']::where('id_jadwal_harian', '=' , $storeData['id'])->count();
        // if ($cekPenuh == 0) {
        //     return response([
        //         'message' => 'Kelas Penuh',
        //     ], 400);
        // }
        // Cek Tarif jika deposit kelas
        if ($storeData['jenis_pembayaran'] == "Deposit Kelas") {
            if (is_null($sisaDeposit)) {
                return response([
                    'message' => 'Deposit Kelas kosong',
                ], 404);
            }
            if ($sisaDeposit['sisa_deposit'] <= 0) {
                return response([
                    'message' => 'Sisa Deposit Tidak Mencukupi',
                ], 404);
            }
            
            // Cek Tarif jika bukan deposit uang
        } else {
            if ($member['deposit_uang'] < $jadwalHarian['FJadwalUmum']['Fkelas']['harga']) {
                return response([
                    'message' => 'Deposit Uang Tidak Mencukupi',
                ], 400);
            }
            
        }

        $date = date("y.m.");
        $id = IdGenerator::generate(['table' => 'presensi_booking_kelas', 'length' => 9, 'prefix' => $date]);
        $storeData['id'] = $id;
        $storeData['tanggal_booking'] = date('Y-m-d');
        $storeData['tanggal_yang_dibooking'] = $jadwalHarian['tanggal_jadwal_harian'];
        $presensiBookingkelas = PresensiBookingKelas::create($storeData);

        return response([
            'message' => 'Berhasil Menambahkan Data',
            'data' => $presensiBookingkelas,
        ], 200);
    }
// Buat PResensi
// if ($storeData['jenis_pembayaran'] == "Deposit Kelas") {
//     if (is_null($sisaDeposit)) {
//         return response([
//             'message' => 'Deposit Kelas kosong',
//         ], 404);
//     }
//     if ($sisaDeposit['sisa_deposit'] <= 0) {
//         return response([
//             'message' => 'Sisa Deposit Tidak Mencukupi',
//         ], 404);
//     }

//     $sisaDeposit = DepositKelas::where([
//         'id_member' => $member['id'],
//         'id_kelas' => $jadwalHarian['FJadwalUmum']['Fkelas']['id'],
//     ])->decrement('sisa_deposit');

//     // Cek Tarif jika bukan deposit uang
// } else {
//     if ($member['deposit_uang'] < $jadwalHarian['FJadwalUmum']['Fkelas']['harga']) {
//         return response([
//             'message' => 'Deposit Uang Tidak Mencukupi',
//         ], 400);
//     }

//     $member['deposit_uang'] = $member['deposit_uang'] - $jadwalHarian['FJadwalUmum']['FKelas']['harga'];
//     $storeData['tarif'] = $jadwalHarian['FJadwalUmum']['FKelas']['harga'];
//     $member->save();
// }

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

    public function getPresensiKelasToday(){
        $presensiBooking = PresensiBookingKelas::with(['FMember', 'FJadwalHarian', ])
        ->where('tanggal_yang_dibooking', '=', date('Y-m-d'))
        ->where('waktu_presensi' , '!=' , null)
        ->get();

        if(count($presensiBooking) >0){
            return response([
                'message' => 'Berhasil Menampilkan Data',
                'data' => $presensiBooking
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null
        ], 200);
    }

    public function getPresnesiBookingKelasById($id){
        $bookingKelas = PresensiBookingKelas::with(['FMember', 'FJadwalHarian', 'FJadwalHarian.FJadwalUmum.FKelas' , 'FJadwalHarian.FInstruktur'])
        ->find($id);
        
        $member = Member::where('id', $bookingKelas->id_member)->first();
        $jadwalHarian = JadwalHarian::where('id', '=', $bookingKelas->id_jadwal_harian)->first();
        $sisaDeposit = DepositKelas::where([
            'id_member' => $member['id'],
            'id_kelas' => $jadwalHarian['FJadwalUmum']['Fkelas']['id'],
        ])->first();
        
        if($bookingKelas){
            return response([
                'message' => 'Berhasil Menampilkan Data',
                'data' => $bookingKelas,
                'sisa_deposit' => $sisaDeposit
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null
        ], 200);
    }

    public function getHistoryBookingM($id){
        $bookingKelas = PresensiBookingKelas::with(['FMember', 'FJadwalHarian', 'FJadwalHarian.FJadwalUmum.FKelas' , 'FJadwalHarian.FInstruktur'])
        ->where('id_member', '=', $id)
        ->get();

        if($bookingKelas){
            return response([
                'message' => 'Berhasil Menampilkan Data',
                'data' => $bookingKelas,
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null
        ], 200);
    }
}
