<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalHarian extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'jadwal_harians';
    protected $fillable = [
        'tanggal_jadwal_harian',
        'id_jadwal_umum',
        'id_instruktur',
        'jam_kelas',
        'hari_kelas',
        'last_update',
    ];

    public function FJadwalUmum()
    {
        return $this->belongsTo(JadwalUmum::class, 'id_jadwal_umum', 'id');
    }
    public function FInstruktur()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id');
    }
}
