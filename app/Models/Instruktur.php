<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class Instruktur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'instruktur';
    protected $fillable = [
        'nama',
        'email',
        'alamat',
        'tanggal_lahir',
        'no_telp',
        'akumulasi_terlambat',
        'password',
    ];

    protected $hidden = ['password',  'remember_token'];
}
