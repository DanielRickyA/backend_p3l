<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDeposiUang extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;
    protected $table = 'transaksi_deposit_uang';
    protected $fillable = [
        'id',
        'id_pegawai',
        'id_member',
        'id_promo',
        'jumlah_depo',
        'tanggal_depo',
        'total_depo',
        'bonus',
        'sisa_saldo',
    ];

    public function FPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id');
    }
    public function FMember()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }
    public function FPromo()
    {
        return $this->belongsTo(Promo::class, 'id_promo', 'id');
    }
}
