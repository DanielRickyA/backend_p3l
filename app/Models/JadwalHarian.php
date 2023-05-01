<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalHarian extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'jadwal_harian';
    protected $fillable = [
        'id_jadwal_umum',
        'id_instruktur',
        'hari_kelas',
        'jam_kelas',
    ];

    public function FJadwalUmum()
    {
        return $this->belongsTo(JadwalUmum::class, 'id_jadwal_umum', 'id');
    }
    public function FInstruktur()
    {
        return $this->belongsTo(Insturktur::class, 'id_instruktur', 'id');
    }
}
