<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiBookingKelas extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;
    protected $table = 'presensi_booking_kelas';
    protected $fillable = [
        'id',
        'id_member',
        'id_jadwal_harian',
        'tanggal_booking',
        'tanggal_yang_dibooking',
        'waktu_presensi',
        'jenis_pembayaran',
        'tarif',
    ];

    public function FMember()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }

    public function FJadwalHarian()
    {
        return $this->belongsTo(JadwalHarian::class, 'id_jadwal_harian', 'id');
    }
}
