<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiBookingGym extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'presensi_booking_gym';
    protected $fillable = [
        'id_member',
        'tanggal_yang_dibooking',
        'tanggal_booking',
        'slot_waktu',
        'jam_presensi',
    ];

    public function FMember()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }
}
