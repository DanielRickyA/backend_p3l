<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'promo';
    protected $fillable = [
        'nama',
        'jenis',
        'isi',
        'bonus',
        'minimal_pembelian',
    ];
}
