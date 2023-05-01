<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUmum extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'jadwal_umum';
    protected $fillable = [
        'id_kelas',
        'id_instruktur',
        'tanggal',
        'hari_kelas',
        'jam_kelas',
    ];

    public function FKelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }

    public function FInstruktur()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id');
    }
}
