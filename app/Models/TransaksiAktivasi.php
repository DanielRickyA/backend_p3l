<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiAktivasi extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;
    protected $table = 'transaksi_aktivasi';
    protected $fillable = [
        'id',
        'id_pegawai',
        'id_member',
        'tanggal_transaksi',
        'jumlah_bayar',
        // 'tanggal_kedaluarsa',
        'jenis_pembayaran',
    ];

    public function FPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id');
    }
    public function FMember()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }
}
