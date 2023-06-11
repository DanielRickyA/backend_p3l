<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositKelas;
use App\Models\JadwalHarian;
use App\Models\Member;
// use App\Models\PresensiBookingKelas;
use App\Models\PresensiBookingKelas;
use App\Models\PresensiInstruktur;
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

        
        $cekPenuh = PresensiBookingKelas::where([
            'id_jadwal_harian' => $request->id_jadwal_harian,
        ])->count();
        if ($cekPenuh == 10) {
            return response([
                'message' => 'Kelas Penuh',
            ], 400);
        }

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
        ], 404);
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
        ], 404);
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
        ], 404);
    }
// mendapatkan hasil booking member order by tanggal dsc
    public function getHistoryBookingMember($id)
    {
        $bookingKelas = PresensiBookingKelas::with(['FMember', 'FJadwalHarian', 'FJadwalHarian.FJadwalUmum.FKelas', 'FJadwalHarian.FInstruktur'])
        ->where('id_member', '=', $id)
        ->where('tanggal_yang_dibooking', '>=', date('Y-m-d'))
        ->where('waktu_presensi', '=', null)
        ->orderBy('tanggal_yang_dibooking', 'desc')
        ->get();

        if ($bookingKelas) {
            return response([
                'message' => 'Berhasil Menampilkan Data',
                'data' => $bookingKelas,
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null
        ], 404);
    }

    public function batalKelas($id){
        $bookingKelas = PresensiBookingKelas::find($id);
        if($bookingKelas->tanggal_yang_dibooking <= date("Y-m-d")) {
            return response([
                'message' => 'Tidak Bisa Batal Booking Kelas',
            ], 400);
        }
        $bookingKelas->delete();
        return response([
            'message' => 'Berhasil membatalkan booking Kelas',
            'data' => $bookingKelas
        ], 200);
    }

    public function getDataBookingKelasInstruktur(Request $request ){
        $jadwalHarian = JadwalHarian::with(['FJadwalUmum.FKelas', 'FInstruktur'])
        ->where('id_instruktur', '=', $request->user()->id)
        ->where('tanggal_jadwal_harian', '=', date('Y-m-d'))
        ->get();
        if(count($jadwalHarian) > 0){
            return response([
                'message' => 'Berhasil Menampilkan Data',
                'data' => $jadwalHarian
            ], 200);
        }
        return response([
            'message' => 'Data Tidak ada',
            'data' => null
        ], 400);
    }

    public function getDataMember($id){
        $bookingKelas = PresensiBookingKelas::with(['FMember', 'FJadwalHarian', 'FJadwalHarian.FJadwalUmum.FKelas', 'FJadwalHarian.FInstruktur'])
        ->where('id_jadwal_harian', '=', $id)
        ->get();

        if(count($bookingKelas) > 0){
            return response([
                'message' => 'Berhasil Menampilkan Data',
                'data' => $bookingKelas
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null
        ], 400);
    }

    
    public function setPresensiHadir($id){
        $presensiMember = PresensiBookingKelas::find($id);

        if ($presensiMember == null) {
            return response([
                'message' => 'Data Tidak ada',
                'data' => null
            ], 404);
        }

        $presensi = PresensiInstruktur::where('id_jadwal_harian', '=', $presensiMember->id_jadwal_harian)->first();
        if($presensi == null){
            return response([
                'message' => 'Instruktur Belum Dipresensi',
                'data' => null
            ], 400);
        }


        

        $jadwalHarian = JadwalHarian::with(['FJadwalUmum.FKelas'])->find($presensiMember->id_jadwal_harian);
        $member = Member::find($presensiMember->id_member);
        if($presensiMember->jenis_pembayaran == "Deposit Kelas"){
             DepositKelas::where([
                'id_member' => $presensiMember->id_member,
                'id_kelas' => $jadwalHarian['FJadwalUmum']['Fkelas']['id'],
            ])->decrement('sisa_deposit');
        }else{
            $member->deposit_uang = $member->deposit_uang - $jadwalHarian['FJadwalUmum']['FKelas']['harga'];
            $member->save();
        }




        $presensiMember->waktu_presensi = date('Y-m-d H:i:s');
        $presensiMember->status = 'Hadir';
        $presensiMember->tarif = $jadwalHarian['FJadwalUmum']['FKelas']['harga'];
        $presensiMember->save();

        return response([
            'message' => 'Berhasil Melakukan Presensi',
            'data' => $presensiMember
        ], 200);
        
    }

    public function setPresensiTidakHadir($id)
    {
        $presensiMember = PresensiBookingKelas::find($id);

        if ($presensiMember == null) {
            return response([
                'message' => 'Data Tidak ada',
                'data' => null
            ], 404);
        }

        $presensi = PresensiInstruktur::where('id_jadwal_harian', '=', $presensiMember->id_jadwal_harian)->first();
        if ($presensi == null) {
            return response([
                'message' => 'Instruktur Belum Dipresensi',
                'data' => null
            ], 400);
        }




        $jadwalHarian = JadwalHarian::with(['FJadwalUmum.FKelas'])->find($presensiMember->id_jadwal_harian);
        $member = Member::find($presensiMember->id_member);
        if ($presensiMember->jenis_pembayaran == "Deposit Kelas") {
            DepositKelas::where([
                'id_member' => $presensiMember->id_member,
                'id_kelas' => $jadwalHarian['FJadwalUmum']['Fkelas']['id'],
            ])->decrement('sisa_deposit');
        } else {
            $member->deposit_uang = $member->deposit_uang - $jadwalHarian['FJadwalUmum']['FKelas']['harga'];
            $member->save();
        }




        $presensiMember->waktu_presensi = null;
        $presensiMember->status = 'Tidak Hadir';
        $presensiMember->tarif = $jadwalHarian['FJadwalUmum']['FKelas']['harga'];
        $presensiMember->save();

        return response([
            'message' => 'Berhasil Melakukan Presensi',
            'data' => $presensiMember
        ], 200);
    }

    

    
    
}
