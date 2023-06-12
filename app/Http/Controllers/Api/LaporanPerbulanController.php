<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPerbulanController extends Controller
{
    public function getGymActivityMonthly($month)
    {
        $laporanGym = DB::select("SELECT tanggal_yang_dibooking as tanggal, count(id_member) as jumlah_member FROM presensi_booking_gym WHERE Left(tanggal_yang_dibooking, 7) = :dateInputan GROUP BY tanggal_yang_dibooking;", [
            'dateInputan' => $month
        ]);

        if (is_null($laporanGym)) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => $laporanGym
        ], 200);
    }

    public function getMonthlyIncome()
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $data = DB::select("SELECT LEFT(ta.tanggal_transaksi, 7) as Bulan, sum(ta.jumlah_bayar) AS Aktivasi, 
            (SELECT sum(tk.jumlah_pembayaran) FROM transaksi_deposit_kelas tk WHERE LEFT(tk.tanggal_depo, 7) = LEFT(ta.tanggal_transaksi, 7)) + 
            (SELECT sum(tu.jumlah_depo) FROM transaksi_deposit_uang tu WHERE LEFT(tu.tanggal_depo, 7) = LEFT(ta.tanggal_transaksi, 7)) as Deposit, 
            sum(ta.jumlah_bayar) + 
            (SELECT sum(tk.jumlah_pembayaran) FROM transaksi_deposit_kelas tk WHERE LEFT(tk.tanggal_depo, 7) = LEFT(ta.tanggal_transaksi, 7)) + 
            (SELECT sum(tu.jumlah_depo) FROM transaksi_deposit_uang tu WHERE LEFT(tu.tanggal_depo, 7) = LEFT(ta.tanggal_transaksi, 7)) as Total FROM transaksi_aktivasi ta GROUP BY Bulan;");
        $dataReturn = [];

        // tambahan langkah ketika ingin looping data di foreach
        $data = json_decode(json_encode($data), true);
        
        for ($i = 1; $i <= 12; $i++) {

            $temp = [
                'Bulan' => $i,
                'Aktivasi' => 0,
                'Deposit' => 0,
                'Total' => 0
            ];

            foreach ($data as $d) {
                // Assume $d["Bulan"] returns "01", "02", ..., "12"
                // $temp = [
                //     'Bulan' => date("Y-") . str_pad($i, 2, '0', STR_PAD_LEFT),
                //     'Aktivasi' => 0,
                //     'Deposit' => 0,
                //     'Total' => 0
                // ];
                if ($d["Bulan"] == date("Y-") . str_pad($i, 2, '0', STR_PAD_LEFT)) {
                    // Data found, perform desired logic here
                    $temp = [
                        'Bulan' => $i,
                        'Aktivasi' => $d["Aktivasi"],
                        'Deposit' => $d["Deposit"],
                        'Total' => $d["Total"]
                    ];
                    break;
                }
            }

            $dataReturn[] = $temp;
        }

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => $dataReturn
        ], 200);
    }


    public function getKelasActivityMonthly($month)
    {
        $date = $month;

        $result = DB::table('presensi_booking_kelas AS pk')
            ->select('k.nama as nama_kelas', 'i.nama', DB::raw('count(pk.id_member) as jumlah_peserta'))
            ->selectSub(function ($query) {
                $query->selectRaw('count(*)')
                    ->from('jadwal_harians')
                    ->where('status', 'Libur')
                    ->whereColumn('jadwal_harians.id', 'pk.id_jadwal_harian')
                    ->limit(1);
            }, 'jumlah_libur')
            ->join('jadwal_harians AS jh', 'pk.id_jadwal_harian', '=', 'jh.id')
            ->join('instruktur AS i', 'jh.id_instruktur', '=', 'i.id')
            ->join('jadwal_umum AS ju', 'jh.id_jadwal_umum', '=', 'ju.id')
            ->join('kelas AS k', 'ju.id_kelas', '=', 'k.id')
            ->whereRaw("LEFT(tanggal_yang_dibooking, 7) = ?", [$date])
            ->groupBy('k.nama', 'i.nama', 'pk.id_jadwal_harian')
            ->get();


        return response()->json([
            'message' => 'Data ditemukan',
            'data' => $result
        ], 200);
    }

    public function getKinerjaInstrukturMonthly($month){
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $laporanGym = DB::select("SELECT i.nama, count(pi.jam_mulai) as jumlah_hadir, 
        (select count(*) FROM jadwal_harians jh where jh.status = 'Libur' 
        AND jh.id = pi.id_jadwal_harian) as jumlah_libur, sum(pi.durasi_terlambat) as waktu_terlambat from presensi_instruktur pi 
            join instruktur i on (pi.id_instruktur = i.id)
            join jadwal_harians jh on (pi.id_jadwal_harian = jh.id) 
            where LEFT(pi.tanggal_kelas, 7) = :dateInputan GROUP BY i.nama, LEFT(pi.tanggal_kelas, 7);", [
            'dateInputan' => $month
        ]);

        if (is_null($laporanGym)) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => $laporanGym
        ], 200);
    }
}
