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

class TransaksiDepoKelasController extends Controller
{
    public function getAllDepositKelas(){
        $depoKelas = TransaksiDepositKelas::with(['FMember', 'FPegawai', 'FKelas', 'FPromo'])->get();
        if(count($depoKelas) > 0){
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $depoKelas
            ], 200);
        }

        return response([
            'message' => 'Data tidak ada',
            'data' => null
        ], 404);
    }

    public function getByIdDK($id)
    {
        $depoK = TransaksiDepositKelas::with(['FPegawai', 'FMember', 'FKelas'])->find($id);
        if (!is_null($depoK)) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $depoK
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null,
        ], 400);
    }

    public function transaksiDepositKelas(Request $request)
    {
        $depoKelas = $request->all();
        $validate = Validator::make(
            $depoKelas,
            [
                'id_pegawai' => 'required',
                'id_member' => 'required',
                'id_promo' => 'integer',
                'id_kelas' => 'required',
                'jumlah_depo' => 'required|numeric',
                'jumlah_pembayaran' => 'required|numeric',
            ]
        );
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        $member = Member::where('id', $request->id_member)->first();
        $kelas = Kelas::where('id', $request->id_kelas)->first();
        if ($member->status == 'Inactive') {
            return response([
                'message' => 'Member Tidak Aktif',
            ], 400);
        }

        $date = date("y.m.");
        $id = IdGenerator::generate(['table' => 'transaksi_deposit_kelas', 'length' => 9, 'prefix' => $date]);
        $depoKelas['id'] = $id;

        $depoKelas['tanggal_depo'] = date('Y-m-d H:i:s');
        $depoKelas['masa_berlaku'] = date('Y-m-d', strtotime('+1 month -1 day'));
        $depoKelas['bonus'] = 0;
        $depoKelas['total_depo'] = $depoKelas['jumlah_depo'];

        if ($depoKelas['id_promo'] != 3) {
            if ($depoKelas['jumlah_depo'] >= 10) {
                $depoKelas['bonus'] = 3;
                $depoKelas['total_depo'] = $depoKelas['jumlah_depo'] + $depoKelas['bonus'];
            } else if ($depoKelas['jumlah_depo'] >= 5) {
                $depoKelas['bonus'] = 1;
                $depoKelas['total_depo'] = $depoKelas['jumlah_depo'] + $depoKelas['bonus'];
            }
        }
        
        
        $totalBayar = $depoKelas['jumlah_depo'] * $kelas['harga'];
        if($depoKelas['jumlah_pembayaran'] < $totalBayar){
            $uangKurang = $totalBayar - $depoKelas['jumlah_pembayaran'];
            return response([
                'message' => 'Jumlah Pembayaran Kurang Rp.' . $uangKurang,
            ], 400);
        }
        $kembalian = $depoKelas['jumlah_pembayaran'] - $totalBayar;
        $depoKelas['jumlah_pembayaran'] = $depoKelas['jumlah_depo'] * $kelas['harga'];
        $transaksiDepositKelas = TransaksiDepositKelas::create($depoKelas);
        $depositKelas = [
            'id_kelas' => $depoKelas['id_kelas'],
            'id_member' => $depoKelas['id_member'],
            'masa_berlaku_depo' => $depoKelas['masa_berlaku'],
            'sisa_deposit' => $depoKelas['total_depo']
        ];

        $hasilDepoKelas = DepositKelas::create($depositKelas);
        return response([
            'message' => 'Transaksi Deposit Kelas Berhasil',
            'data' => ['Trasnsaksi_Depo_kelas' => $transaksiDepositKelas, 'deposit_kelas' => $hasilDepoKelas],
            'kembalian' => $kembalian
        ], 200);
    }

    public function tampilDepositKelas(){
        $depoKelas = DepositKelas::with(['FMember', 'FKelas'])->where('masa_berlaku_depo' , '>=', date('Y-m-d'))->get();
        if(count($depoKelas) > 0){
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $depoKelas
            ], 200);
        }
        return response([
            'message' => 'Data tidak ada',
            'data' => null
        ], 404);

    }
}
