<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promo = Promo::all();

        if (count($promo) > 0) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Tidak ada data',
            'data' => null
        ], 404);
    }
}
