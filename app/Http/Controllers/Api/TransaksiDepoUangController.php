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

class TransaksiDepoUangController extends Controller
{
    public function getAllDepositUang()
    {
        $depoUang = TransaksiDeposiUang::with(['FMember', 'FPegawai', 'FPromo'])->get();
        if (count($depoUang) > 0) {
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $depoUang
            ], 200);
        }

        return response([
            'message' => 'Data tidak ada',
            'data' => null
        ], 404);
    }

    public function getByIdDU($id)
    {
        $depoU = TransaksiDeposiUang::with(['FMember', 'FPegawai'])->find($id);
        if (!is_null($depoU)) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $depoU
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null,
        ], 400);
    }

    public function transaksiDepositUang(Request $request)
    {
        $depoUang = $request->all();
        $validate = Validator::make(
            $depoUang,
            [
                'id_pegawai' => 'required',
                'id_member' => 'required',
                'id_promo' => 'integer',
                'jumlah_depo' => 'required|numeric',
            ]
        );

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        $member = Member::where('id', $request->id_member)->first();
        if ($member->status == 'Inactive') {
            return response([
                'message' => 'Member Tidak Aktif',
            ], 400);
        }

        $date = date("y.m.");
        $id = IdGenerator::generate(['table' => 'transaksi_deposit_uang', 'length' => 9, 'prefix' => $date]);
        $depoUang['id'] = $id;
        $depoUang['bonus'] = 0;
        $depoUang['tanggal_depo'] = date('Y-m-d H:i:s');
        $depoUang['sisa_saldo'] = $member['deposit_uang'];
        $depoUang['total_depo'] = $depoUang['jumlah_depo'] +  $depoUang['sisa_saldo'];

        if ($depoUang['id_promo'] != 3) {
            if ($depoUang['jumlah_depo'] < 500000) {
                return response([
                    'message' => 'Minimal Deposit Rp. 500.000',
                ], 400);
            }

            if ($depoUang['jumlah_depo'] >= 3000000) {
                $depoUang['bonus'] = 300000;
                $depoUang['total_depo'] = $depoUang['jumlah_depo'] + $depoUang['bonus'] + $depoUang['sisa_saldo'];
            }
        }

        $depoUang['sisa_saldo'] = $member['deposit_uang'];
        // $depoUang['total_depo'] = $depoUang['jumlah_depo'] +  $depoUang['sisa_saldo'];

        $member['deposit_uang'] = $depoUang['total_depo'];
        $member->save();
        $transaksiDepoUang = TransaksiDeposiUang::create($depoUang);


        return response([
            'message' => 'Transaksi Deposit Uang Berhasil',
            'data' => $transaksiDepoUang,
        ], 200);
    }
}
