<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalHarian;
use App\Models\PerizinanInstruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IjinInstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ijinInstrktur = PerizinanInstruktur::with(['FInstruktur'])->where('status', '=', null)->get();
        if (count($ijinInstrktur) > 0) {
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $ijinInstrktur
            ], 200);
        }
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
    public function requestIzin(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_jadwal' => 'required',
            'keterangan' => 'required',
        ]);
        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);


        $instruktur = $request->user();
        $jadwalInstruktur = JadwalHarian::where('id', '=', $request->id_jadwal)->where('id_instruktur', '=', $instruktur->id)->first();
        if ($jadwalInstruktur == null)
            return response(['message' => 'Jadwal tidak ditemukan'], 404);

        $izinInstruktur = PerizinanInstruktur::create([
            'id_instruktur' => $instruktur->id,
            'tanggal_izin' => $jadwalInstruktur->tanggal_jadwal_harian,
            'keterangan' => $request->keterangan,
            'tanggal_buat_izin' => date('Y-m-d'),
            'status' => null,
            'tanggal_konfirm' => null,
        ]);

        return response([
            'message' => 'Berhasil membuat permintaan izin',
            'data' => $izinInstruktur
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showJadwalInsturktur(Request $request)
    {
        $instruktur = $request->user();
        $jadwalInstruktur = JadwalHarian::with(['FInstruktur', 'FJadwalUmum'])->where('id_instruktur', '=', $instruktur->id)->get();
        if (!is_null($jadwalInstruktur)) {
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $jadwalInstruktur
            ], 200);
        }

        return response([
            'message' => 'Tidak ada data',
            'data' => null
        ], 404);
    }

    public function showIzinInsturktur(Request $request)
    {
        $instruktur = $request->user();
        $ijin = PerizinanInstruktur::with(['FInstruktur'])->where('id_instruktur', '=', $instruktur->id)->get();
        if (!is_null($ijin)) {
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $ijin
            ], 200);
        }

        return response([
            'message' => 'Tidak ada data',
            'data' => null
        ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function konfirmPerizinan(Request $request, $id)
    {
        $perizinan = PerizinanInstruktur::findOrFail($id);
        if (is_null($perizinan)) {
            return response([
                'message' => 'Perizinan tidak ditemukan',
                'data' => null
            ], 404);
        }
        $perizinan->status = '1';
        $perizinan->tanggal_konfirm = date('Y-m-d');
        $perizinan->save();
        $ins = $perizinan->FInstruktur->id;
        
        $jadwal = JadwalHarian::where('id_instruktur', '=', $ins)->where('tanggal_jadwal_harian', '=', $perizinan->tanggal_izin)->first();
        if (is_null($jadwal)) {
            return response([
                'message' => 'Jadwal tidak ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_instruktur' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        if (self::cekInsturktur($updateData['id_instruktur'], $jadwal->tanggal_jadwal_harian, $jadwal->FJadwalUmum->jam_kelas) > 0) {

            return response(['message' => 'Jadwal Instruktur Bertabrakan'], 400);
        }
        $jadwal->status = 'Instruktur Digantikan';
        $jadwal->save();
        return response([
            'message' => 'Berhasil menerima permintaan izin',
            'data' => $perizinan,
            'jadwal' => $jadwal
        ], 200);
    }

    private static function cekInsturktur($id, $tanggal, $jam)
    {

        $count = JadwalHarian::where('id_instruktur', '=', $id)
            ->where('tanggal_jadwal_harian', '=', $tanggal)
            ->where('jam_kelas', '=', $jam)
            ->count();


        return $count;
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
    public function tolakPerizinan($id)
    {
        $perizinan = PerizinanInstruktur::find($id);
        $perizinan->status = '0';
        $perizinan->tanggal_konfirm = date('Y-m-d');
        $perizinan->save();
    }
}
