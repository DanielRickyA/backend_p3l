<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalHarian;
use App\Models\JadwalUmum;
use App\Models\Instruktur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalHarianController extends Controller
{
    public function index()
    {
        if (JadwalHarian::count() == 0) {
            return response([
                'message' => 'No Data',
            ], 200);
        }


        $jadwal = JadwalHarian::orderBy('last_update', 'desc')->first();

        $jadwalHarian = JadwalHarian::with(['FJadwalUmum', 'FJadwalUmum.fInstruktur', 'FJadwalUmum.fKelas',  'FInstruktur'])->where(
            'last_update',
            $jadwal->last_update
        )->orderBy('tanggal_jadwal_harian', 'asc')->get();


        if (count($jadwalHarian) > 0) {
            return response([
                'message' => 'Data Successfully',
                'data' => $jadwalHarian
            ], 200);
        }
    }

    public function generateJadwalHarian()
    {
        
        $jadwalUmum = JadwalUmum::get();
        $startDate = Carbon::now('Asia/Jakarta');
        $endDate = $startDate->copy()->addDays(6);

        $existingJadwalHarian = JadwalHarian::whereBetween('tanggal_jadwal_harian', [$startDate, $endDate])->get();

        $lateDateCheck = JadwalHarian::orderBy('last_update', 'desc')->first();
        $lateDateCheckCarbon = Carbon::parse($lateDateCheck->last_update);
        $lastDateCheck = $lateDateCheckCarbon->copy()->dayOfWeekIso;

        $temp = 7 - $lastDateCheck;

        if ($startDate <= $lateDateCheckCarbon->copy()->addDays($temp)) {
            return response([
                'message' => 'Jadwal Harian Sudah Dibuat',
            ], 400);
        }

        // $daysFromToday = $startDate->diffInDays($lateDateCheckCarbon->addDays($temp));

        // if($daysFromToday <= 0){
        //     return response([
        //         'message' => 'Jadwal Harian Sudah Dibuat',
        //     ], 400);
        // }



        if ($existingJadwalHarian->isNotEmpty()) {
            return response([
                'message' => 'Jadwal harian untuk minggu ini sudah digenerate.',
                'data' => $lateDateCheckCarbon->addDays($temp),
                'startDate' => $startDate,
            ], 400);
        }

        foreach ($jadwalUmum as $item) {
            $hari_ke = Carbon::parse($item->hari_kelas)->dayOfWeekIso;

            $tanggal = null;
            if ($startDate->dayOfWeekIso >= $hari_ke) {
                $tambahHari = 7 - ($startDate->dayOfWeekIso - $hari_ke);
                $tanggal = $startDate->copy()->addDays($tambahHari);
            } else {
                $kurangHari = $startDate->dayOfWeekIso  - $hari_ke;
                $tanggal = $startDate->copy()->subDays($kurangHari);
            }

            while ($tanggal->lte($endDate)) {
                $jadwalHarian = new JadwalHarian();
                $jadwalHarian->tanggal_jadwal_harian = $tanggal;
                $jadwalHarian->id_jadwal_umum = $item->id;
                $jadwalHarian->id_instruktur = $item->id_instruktur;
                $jadwalHarian->jam_kelas = $item->jam_kelas;
                $jadwalHarian->status = 'Masuk';
                $jadwalHarian->last_update = Carbon::now();
                $jadwalHarian->save();

                $tanggal->addWeek();
            }
        }

        return response([
            'message' => 'Jadwal Harian Berhasil Digenerate untuk satu minggu',
            'lateDate' => $lateDateCheckCarbon->addDays($temp),
            'startDate' => $startDate,
        ], 200);
        // $jadwalUmum = JadwalUmum::get();
        // $startDate = Carbon::today();
        // $endDate = $startDate->copy()->addDays(6);

        // $existingJadwalHarian = JadwalHarian::whereBetween('tanggal_jadwal_harian', [$startDate, $endDate])->get();

        // $lateDateCheck = JadwalHarian::orderBy('last_update', 'desc')->first();
        // $lastDateCheck = Carbon::parse($lateDateCheck->last_update)->dayOfWeekIso;
        // $temp = 7-

        // if ($existingJadwalHarian->isNotEmpty()) {
        //     return response([
        //         'message' => 'Jadwal harian untuk minggu ini sudah digenerate.',
        //     ], 400);
        // }

        // foreach ($jadwalUmum as $item) {
        //     $hari_ke = Carbon::parse($item->hari_kelas)->dayOfWeekIso;

        //     $tanggal = null;
        //     if ($startDate->dayOfWeekIso >= $hari_ke) {
        //         $tambahHari = 7 - ($startDate->dayOfWeekIso - $hari_ke);
        //         $tanggal = $startDate->copy()->addDays($tambahHari);
        //     } else {
        //         $kurangHari = $startDate->dayOfWeekIso  - $hari_ke ;
        //         $tanggal = $startDate->copy()->subDays($kurangHari);
        //     }

        //     while ($tanggal->lte($endDate)) {
        //         $jadwalHarian = new JadwalHarian();
        //         $jadwalHarian->tanggal_jadwal_harian = $tanggal;
        //         $jadwalHarian->id_jadwal_umum = $item->id;
        //         $jadwalHarian->id_instruktur = $item->id_instruktur;
        //         $jadwalHarian->jam_kelas = $item->jam_kelas;
        //         $jadwalHarian->status = 'Masuk';
        //         $jadwalHarian->last_update = Carbon::now();
        //         $jadwalHarian->save();

        //         $tanggal->addWeek();
        //     }
        // }

        // return response([
        //     'message' => 'Jadwal Harian Berhasil Digenerate untuk satu minggu',
        // ], 200);
    }

    public function changeStatus1($id)
    {
        $jadwalHarian = JadwalHarian::with(['FJadwalUmum', 'FJadwalUmum.fInstruktur', 'FJadwalUmum.fKelas',  'FInstruktur'])->find([$id]);

        if (is_null($jadwalHarian)) {
            return response([
                'message' => 'Jadwal Harian Tidak Ditemukan',
                'data' => $jadwalHarian
            ], 200);
        }

        if ($jadwalHarian->status == 'Masuk') {
            $jadwalHarian->status = 'Libur';
            $jadwalHarian->save();
            return response([
                'message' => 'Berhasil Mengubah status',
                'data' => $jadwalHarian
            ], 200);
        }

        if ($jadwalHarian->status == 'Libur') {
            $jadwalHarian->status = 'Masuk';
            $jadwalHarian->save();
            return response([
                'message' => 'Berhasil Mengubah status',
                'data' => $jadwalHarian
            ], 200);
        }

        return response([
            'message' => 'Gagal Mengubah status',
            'data' => null
        ], 404);
    }

    public function changeStatus($tanggal, $id_jadwal_umum)
    {
        $jadwalHarian = JadwalHarian::with(['FJadwalUmum', 'FJadwalUmum.fInstruktur', 'FJadwalUmum.fKelas',  'FInstruktur'])
            ->where([
                'tanggal_jadwal_harian' => $tanggal,
                'id_jadwal_umum' => $id_jadwal_umum
            ])->first();

        if (is_null($jadwalHarian)) {
            return response([
                'message' => 'Jadwal Harian Tidak Ditemukan',
                'data' => $jadwalHarian
            ], 200);
        }

        $status = $jadwalHarian->status === 'Masuk' ? 'Libur' : 'Masuk';

        $affectedRows = DB::table('jadwal_harian')
            ->where([
                'tanggal_jadwal_harian' => $tanggal,
                'id_jadwal_umum' => $id_jadwal_umum
            ])
            ->update(['status' => $status]);

        if ($affectedRows) {
            $jadwalHarian->status = $status;

            return response([
                'message' => 'Berhasil Mengubah status',
                'data' => $jadwalHarian
            ], 200);
        }

        return response([
            'message' => 'Gagal Mengubah status',
            'data' => null
        ], 404);
    }

    public function getjadwalHarianM()
    {
        if (JadwalHarian::count() == 0) {
            return response([
                'message' => 'No Data',
            ], 200);
        }


        $jadwal = JadwalHarian::orderBy('last_update', 'desc')->first();

        $jadwalHarian = JadwalHarian::with(['FJadwalUmum', 'FJadwalUmum.fInstruktur', 'FJadwalUmum.fKelas',  'FInstruktur'])->where(
            'last_update',
            $jadwal->last_update
        )->where('tanggal_jadwal_harian', '>=', date('Y-m-d'))->orderBy('tanggal_jadwal_harian', 'asc')->get();


        if (count($jadwalHarian) > 0) {
            return response([
                'message' => 'Data Successfully',
                'data' => $jadwalHarian
            ], 200);
        }
    }

    public function getjadwalHarian()
    {
        if (JadwalHarian::count() == 0) {
            return response([
                'message' => 'No Data',
            ], 200);
        }


        $jadwal = JadwalHarian::orderBy('last_update', 'desc')->first();

        $jadwalHarian = JadwalHarian::with(['FJadwalUmum', 'FJadwalUmum.fInstruktur', 'FJadwalUmum.fKelas',  'FInstruktur'])->where(
            'last_update',
            $jadwal->last_update
        )->where('tanggal_jadwal_harian', '>=', date('Y-m-d'))->orderBy('tanggal_jadwal_harian', 'asc')->get();


        if (count($jadwalHarian) > 0) {
            return response([
                'message' => 'Data Successfully',
                'data' => $jadwalHarian
            ], 200);
        }
    }

    public function getjadwalHarianToday()
    {
        if (JadwalHarian::count() == 0) {
            return response([
                'message' => 'No Data',
            ], 200);
        }


        $jadwal = JadwalHarian::orderBy('last_update', 'desc')->first();

        $jadwalHarian = JadwalHarian::with(['FJadwalUmum', 'FJadwalUmum.fInstruktur', 'FJadwalUmum.fKelas',  'FInstruktur'])->where(
            'last_update',
            $jadwal->last_update
        )->where('tanggal_jadwal_harian', '=', date('Y-m-d'))->orderBy('tanggal_jadwal_harian', 'asc')->get();


        if (count($jadwalHarian) > 0) {
            return response([
                'message' => 'Data Successfully',
                'data' => $jadwalHarian
            ], 200);
        }
    }
}
