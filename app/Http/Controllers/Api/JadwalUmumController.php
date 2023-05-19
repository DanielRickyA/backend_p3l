<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\JadwalUmum;
use App\Models\Kelas;
use App\Models\Instruktur;

class JadwalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $jadwalUmum = JadwalUmum::with(['FKelas', 'FInstruktur'])->get();
        if (count($jadwalUmum) > 0) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $jadwalUmum
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null,
        ], 400);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    private static function cekInsturktur($id, $hari, $jam)
    {

        $count = JadwalUmum::where('id_instruktur', '=', $id)
        ->where('hari_kelas', '=', $hari)
        ->where('jam_kelas', '=', $jam)
        ->count();


        return $count;
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
            'id_kelas' => 'required',
            // making id_insturktur, hari_kelas, and jam_kelas unique in the same time
            'id_instruktur' => 'required',
            'hari_kelas' => 'required|string',
            'jam_kelas' => 'required|string',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        if(self::cekInsturktur($request->id_instruktur, $request->hari_kelas, $request->jam_kelas) > 0){
            return response([
                'message' => 'Instruktur sudah memiliki jadwal dihari dan jam samas',
            ], 400);
        }

        $checkKelas = Kelas::where('id', $request->id_kelas)->first();
        if (!$checkKelas) {
            return response([
                'message' => 'Kelas Tidak ada',
            ], 400);
        }

        $checkInstruktur = Instruktur::where('id', $request->id_instruktur)->first();
        if (!$checkInstruktur) {
            return response([
                'message' => 'Instruktur Tidak ada',
            ], 400);
        }

        $storeData['id'] = 
        $jadwalUmum = JadwalUmum::create($storeData);
        return response([
            'message' => 'Berhasil Menambahkan Jadwal',
            'data' => $jadwalUmum,
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
        $jadwalUmum = JadwalUmum::with(['FKelas', 'FInstruktur'])->find($id);

        if (!is_null($jadwalUmum)) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $jadwalUmum
            ], 200);
        }

        return response([
            'message' => 'Data Tidak ada',
            'data' => null,
        ], 400);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $jadwalUmum = JadwalUmum::find($id);
        if (is_null($jadwalUmum)) {
            return response([
                'message' => 'Data Tidak ada',
                'data' => null,
            ], 400);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_kelas' => 'required',
            'id_instruktur' => 'required',
            'hari_kelas' => 'required|string',
            'jam_kelas' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $checkKelas = Kelas::where('id', $request->id_kelas)->first();
        if (!$checkKelas) {
            return response([
                'message' => 'Kelas Tidak ada',
            ], 400);
        }

        $checkInstruktur = Instruktur::where('id', $request->id_instruktur)->first();
        if (!$checkInstruktur) {
            return response([
                'message' => 'Instruktur Tidak ada',
            ], 400);
        }

        $jadwalUmum->id_kelas = $updateData['id_kelas'];
        $jadwalUmum->id_instruktur = $updateData['id_instruktur'];
        $jadwalUmum->hari_kelas = $updateData['hari_kelas'];
        $jadwalUmum->jam_kelas = $updateData['jam_kelas'];

        if ($jadwalUmum->save()) {
            return response([
                'message' => 'Berhasil Mengubah Jadwal',
                'data' => $jadwalUmum,
            ], 200);
        }

        return response([
            'message' => 'Gagal Mengubah Jadwal',
            'data' => null,
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jadwalUmum = JadwalUmum::find($id);

        if (is_null($jadwalUmum)) {
            return response([
                'message' => 'Data Tidak ada',
                'data' => null,
            ], 400);
        }

        if ($jadwalUmum->delete()) {
            return response([
                'message' => 'Berhasil Menghapus Jadwal',
                'data' => $jadwalUmum,
            ], 200);
        }

        return response([
            'message' => 'Gagal Menghapus Jadwal',
            'data' => null,
        ], 400);
    }
}
