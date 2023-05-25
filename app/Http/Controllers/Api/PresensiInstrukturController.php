<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalHarian;
use App\Models\PresensiInstruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PresensiInstrukturController extends Controller
{
    public function SetPresensInstruktur(Request $request){
        $storeData = $request->all();
        $validate = Validator::make(
            $storeData,
            [
                'id_jadwal_harian' => 'required',
            ]
        );
        if($validate->fails()){
            return response(['message' => $validate->errors()],400);
        }
        $jadwalHarian = JadwalHarian::find($storeData['id_jadwal_harian']);
        if(!$jadwalHarian){
            return response([
                'message' => 'Jadwal Harian tidak ditemukan',

        ],404);
        }
        $storeData['id_instruktur'] = $jadwalHarian->id_instruktur;
        $storeData['tanggal_kelas'] = $jadwalHarian->tanggal_jadwal_harian;
        $storeData['jam_mulai'] = date('H:i:s');
        $storeData['durasi_kelas'] = 3600;

        $presensiInstruktur = PresensiInstruktur::create($storeData);
        return response([
            'message' => 'Berhasil menambahkan data',
            'data' => $presensiInstruktur
            
        ],200);
        
    }

    public function getPresensiToday(){
        $presensiInstruktur = PresensiInstruktur::with(['FInstruktur','FJadwalHarian'])
        ->where('tanggal_kelas',date('Y-m-d'))
        ->where("jam_selesai", null)
        ->get();
        if(count($presensiInstruktur) > 0){
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $presensiInstruktur
            ],200);
        }
        return response([
            'message' => 'Tidak ada data',
            'data' => null
        ],404);
    }

    public function getPresensiAllToday()
    {
        $presensiInstruktur = PresensiInstruktur::with(['FInstruktur', 'FJadwalHarian'])
        ->where('tanggal_kelas', date('Y-m-d'))
            ->get();
        if (count($presensiInstruktur) > 0) {
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $presensiInstruktur
            ], 200);
        }
        return response([
            'message' => 'Tidak ada data',
            'data' => null
        ], 404);
    }

    public function setJamSelesaiPresensi($id){
        $presensiInstruktur = PresensiInstruktur::find($id);
        if(!$presensiInstruktur){
            return response([
                'message' => 'Data tidak ditemukan',
                'data' => null
            ],404);
        }
        $presensiInstruktur->jam_selesai = date('H:i:s');
        $presensiInstruktur->save();
        return response([
            'message' => 'Berhasil mengubah data',
            'data' => $presensiInstruktur
        ],200);
    }

}
