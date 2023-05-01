<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class Pegawai extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'pegawai';
    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'alamat',
        'no_telp',
        'role',
        'password',
    ];

    protected $hidden = ['password',  'remember_token'];
}
