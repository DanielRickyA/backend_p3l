<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{
    public function username()
    {
        return 'nama';
    }

    public function loginPegawai(Request $request)
    {
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'nama' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        if (!Auth::guard('pegawai')->attempt($loginData))
            return response(['message' => 'Invalid Credentials'], 401);

        $guard = Auth::guard('pegawai')->user();
        // $pegawai = Auth::pegawai();
        $token = $guard->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $guard,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function LogoutPegawai(Request $request)
    {
        $request->user()->token()->revoke();
        return response([
            'message' => 'Logout Success'
        ], 200);
    }


    public function loginInstruktur(Request $request)
    {
        $loginInstruktur = $request->all();
        $validator = Validator::make($loginInstruktur, [
            'nama' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if (!Auth::guard('instruktur')->attempt($loginInstruktur)) {
            return response(['message' => 'Invalid Credentials'], 401);
        }
        $guard = Auth::guard('instruktur')->user();
        $token = $guard->createToken('authToken')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $guard,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function LogoutInstruktur(Request $request)
    {
        $token = $request->instruktur()->token();
        $token->revoke();
        return response([
            'message' => 'Logout Success'
        ], 200);
    }
}
