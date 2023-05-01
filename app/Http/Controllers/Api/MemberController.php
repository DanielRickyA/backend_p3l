<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Member;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $member = Member::all();

        if (count($member) > 0) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $member,
                'userLoggedIn' => Auth::guard('pegawai')->user()
            ], 200);
        }

        return response([
            'message' => 'Tidak ada data',
            'data' => null
        ], 404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make(
            $storeData,
            [
                'nama' => 'required',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required',
                'no_telp' => 'required|',

            ]
        );
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $date = date("y.m.");
        $id = IdGenerator::generate(['table' => 'member', 'length' => 8, 'prefix' => $date]);
        $storeData['status'] = 'Mati';
        $storeData['id'] = $id;
        $storeData['password'] = bcrypt($storeData['tanggal_lahir']);
        $member = Member::create($storeData);
        return response([
            'message' => 'Berhasil Menambahkan Member',
            'data' => $member
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $member = Member::find($id);

        if (!is_null($member)) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $member
            ], 200);

            return response([
                'message' => 'Member tidak ditemukan',
                'data' => null
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        if (is_null($member)) {
            return response([
                'message' => 'Member tidak ditemukan',
                'data' => null
            ], 404);
        }

        $update = $request->all();
        $validate = Validator::make($update, [
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'no_telp' => 'required',
            'status' => 'string',
            'password' => 'string|min:8',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $member->nama = $update['nama'];
        $member->tanggal_lahir = $update['tanggal_lahir'];
        $member->alamat = $update['alamat'];
        $member->no_telp = $update['no_telp'];
        if(isset($update['password'])){
            $member->password = bcrypt($update['password']);
        }
        

        // $member->status = $update['status'];

        if ($member->save()) {
            return response([
                'message' => 'Berhasil Mengubah Member',
                'data' => $member,
            ], 200);
        }

        return response([
            'message' => 'Gagal Mengubah Member',
            'data' => null,
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = Member::find($id);

        if (is_null($member)) {
            return response([
                'message' => 'Member tidak ditemukan',
                'data' => null
            ], 404);
        }

        if ($member->delete()) {
            return response([
                'message' => 'Berhasil Menghapus Member',
                'data' => $member,
            ], 200);
        }

        return response([
            'message' => 'Gagal Menghapus Member',
            'data' => null,
        ], 400);
    }

    public function resetPassword($id)
    {
        $member = Member::find($id);

        if (is_null($member)) {
            return response([
                'message' => 'Member tidak ditemukan',
                'data' => null
            ], 404);
        }

        $member->password = bcrypt($member->tanggal_lahir);
        if ($member->save()) {
            return response([
                'message' => 'Berhasil Mereset Password Member',
                'data' => $member,
            ], 200);
        }

        return response([
            'message' => 'Gagal Mereset Password Member',
            'data' => null,
        ], 400);
    }
}
