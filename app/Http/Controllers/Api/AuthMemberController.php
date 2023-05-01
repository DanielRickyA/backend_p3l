<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;

class AuthMemberController extends Controller
{
    public function username()
    {
        return 'id';
    }

    public function loginMember(Request $request)
    {
        $loginMember = $request->all();
        $validator = Validator::make($loginMember, [
            'id' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if (!Auth::guard('member')->attempt($loginMember)) {
            return response(['message' => 'Invalid Credentials'], 401);
        }
        $guard = Auth::guard('member')->user();
        $token = $guard->createToken('authToken')->accessToken;

        return response([
            'message' => 'Login Success',
            'user' => $guard,
            'token' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function LogoutMember(Request $request)
    {
        $token = Auth::guard('member')->user()->token_name;
        $token->revoke();
        return response([
            'message' => 'Logout Success'
        ], 200);
    }
}
