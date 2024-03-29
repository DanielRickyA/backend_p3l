<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerizinanInstruktur extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'perizinan_instruktur';
    protected $fillable = [
        'id_instruktur',
        'instruktur_pengganti',
        'tanggal_izin',
        'tanggal_buat_izin',
        'status',
        'keterangan',
        'tanggal_konfirm'
    ];

    public function FInstruktur()
    {
        return $this->belongsTo(Instruktur::class, 'id_instruktur', 'id');
    }

    public function FPengganti(){
        return $this->belongsTo(Instruktur::class, 'instruktur_pengganti', 'id');
    }
}
