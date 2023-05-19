<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDepositKelas extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;
    protected $table = 'transaksi_deposit_kelas';
    protected $fillable = [
        'id',
        'id_pegawai',
        'id_member',
        'id_kelas',
        'id_promo',
        'tanggal_depo',
        'masa_berlaku',
        'bonus',
        'jumlah_depo',
        'total_depo',
        'jumlah_pembayaran',
    ];

    public function FPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id');
    }
    public function FMember()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }
    public function FKelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
    public function FPromo()
    {
        return $this->belongsTo(Promo::class, 'id_promo', 'id');
    }
}
