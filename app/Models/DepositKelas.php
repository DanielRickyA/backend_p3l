<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositKelas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'deposit_kelas';
    protected $fillable = [
        'id_kelas',
        'id_member',
        'masa_berlaku_depo',
        'sisa_deposit',
    ];

    public function FKelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
    public function FMember()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id');
    }
}
