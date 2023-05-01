<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Member extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;
    protected $table = 'member';
    protected $fillable = [
        'id',
        'nama',
        'tanggal_lahir',
        'alamat',
        'no_telp',
        'status',
        'tanggal_expired',
        'deposit_uang',
        'password',
    ];

    protected $hidden = ['password',  'remember_token'];
}
