<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\Member;
use App\Models\Pegawai;
use App\Models\Kelas;
use App\Models\TransaksiAktivasi;
use App\Models\TransaksiDepositKelas;
use App\Models\TransaksiDeposiUang;
use Psy\Readline\Hoa\Console;

class TransaksiAktivasiController extends Controller
{

    public function getAllAktivasi(){
        $aktivasi = TransaksiAktivasi::with(['FMember', 'FPegawai'])->get();
        if(count($aktivasi) > 0){
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $aktivasi
            ], 200);
        }

        return response([
            'message' => 'Data tidak ada',
            'data' => null
        ], 404);
    }

    public function getByIdAktivasi($id)
    {
        $aktivasi = TransaksiAktivasi::with(['FMember', 'FPegawai'])->find($id);
        if (!is_null($aktivasi)) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $aktivasi
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null,
        ], 400);
    
    }
    
    public function transaksiAktivasi(Request $request)
    {
        $aktivasi = $request->all();
        $validate = Validator::make(
            $aktivasi,
            [
                'id_pegawai' => 'required',
                'id_member' => 'required',
                'jumlah_bayar' => 'required|numeric|min:3000000',
                'jenis_pembayaran' => 'required',
            ]
        );

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $member = Member::where('id', $request->id_member)->first();
        if ($member->status == 'Active') {
            return response([
                'message' => 'Member Sudah Aktif',
            ], 400);
        }

        $member->status = 'Active';
        $member->tanggal_expired = date('Y-m-d', strtotime('+1 year -1 day'));
        $member->save();

        $date = date("y.m.");
        $id = IdGenerator::generate(['table' => 'transaksi_aktivasi', 'length' => 9, 'prefix' => $date]);
        $aktivasi['id'] = $id;

        $aktivasi['tanggal_transaksi'] = date('Y-m-d H:i:s');
        $transaksiAktivasi = TransaksiAktivasi::create($aktivasi);

        return response([
            'message' => 'Transaksi Aktivasi Berhasil',
            'data' => $transaksiAktivasi,
        ], 200);
    }

    public function deactivasiMember($id){
        $member = Member::find($id);
        if(is_null($member)){
            return response([
                'message' => 'Member tidak ditemukan',
                'data' => null
            ], 404);
        }
        if($member->status == 'Inactive'){
            return response([
                'message' => 'Member sudah tidak aktif',
                'data' => null
            ], 400);
        }

        if($member->tanggal_expired > date('Y-m-d')){
            return response([
                'message' => 'Tanggal Expired Member Masih Aktif',
                'data' => null
            ], 404);
        }

        $member->status = 'Inactive';
        $member->tanggal_expired = null;
        $member->save();

        return response([
            'message' => 'Member berhasil di non aktifkan',
            'data' => $member
        ], 200);
    }

   

    
}
