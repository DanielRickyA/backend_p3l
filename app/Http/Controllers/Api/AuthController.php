<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instruktur;
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
            'message' => 'Logout Success', 
        ], 200);
    }

    public function ChangePasswordPegawai(Request $request)
    {
        $pegawai = $request->all();
        $validator = Validator::make($pegawai, [
            'nama' => 'required',
            'password' => 'required',
        ],);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // $peg = Pegawai::where('nama', $pegawai['nama'])->first();
        // checking Pegawai the name from the $pegawai and pegawai role is MO
        $peg = Pegawai::where('nama', $pegawai['nama'])->where('role', 'MO')->first();

        if (is_null($peg)) {
            return response()->json([
                'message' => 'Pegawai not found'
            ], 404);
        }
        $peg->password = bcrypt($pegawai['password']);
        $peg->save();

        return response()->json([
            'message' => 'Pegawai password changed'
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

    public function ChangePasswordInsturktur(Request $request){
        $instruktur = $request->all();
        $validator = Validator::make($instruktur, [
            'nama' => 'required',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        
        $ins = Instruktur::where('nama', $instruktur['nama'])->first();

        if(is_null($ins)){
            return response()->json([
                'message' => 'Instruktur not found'
            ], 404);
        }
        $ins->password = bcrypt($instruktur['password']);
        $ins->save();

        return response()->json([
            'message' => 'Instruktur password changed'
        ], 200);
    }
}
