<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiBookingKelas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'presensi_booking_kelas';
    protected $fillable = [
        'id_member',
        'tanggal_jadwal_harian',
        'tanggal_yang_dibooking',
        'tanggal_booking',
        'waktu_presensi',
        'tarif',
    ];

    public function FMember()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }

    public function FJadwalHarian()
    {
        return $this->belongsTo(JadwalHarian::class, 'tanggal_jadwal_harian', 'tanggal_jadwal_harian');
    }
}
