<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositKelas;
use Illuminate\Http\Request;

class DepositKelasController extends Controller
{
    public function showDepoKelasMember($id){
        $depositKelas = DepositKelas::with(['FMember', 'FKelas'])
        ->where('id_member', $id)
        ->where('masa_berlaku_depo', '>', date('Y-m-d'))
        ->get();

        if (count($depositKelas) > 0) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $depositKelas,

            ], 200);
        }

        return response([
            'message' => 'Tidak ada data',
            'data' => null
        ], 404);
    }
}
