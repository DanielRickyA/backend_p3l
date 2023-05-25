<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiInstruktur extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'presensi_instruktur';
    protected $fillable = [
        'id_instruktur',
        'id_jadwal_harian', 
        'tanggal_kelas',
        'jam_mulai',
        'jam_selesai',
        'durasi_kelas',
        'durasi_terlambat'
    ];

    public function FInstruktur()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id');
    }
    
    public function FJadwalHarian()
    {
        return $this->belongsTo(JadwalHarian::class, 'id_jadwal_harian', 'id');
    }
}
