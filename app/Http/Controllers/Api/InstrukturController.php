<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Instruktur;

class InstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $instruktur = Instruktur::all();

        if (count($instruktur) > 0) {
            return response([
                'message' => 'Berhasil Menerima data',
                'data' => $instruktur
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
        $validate = Validator::make($storeData, [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required',
            'tanggal_lahir' => 'required|date',
            'no_telp' => 'required',
            'password' => 'required|string|min:8',
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $storeData['password'] = bcrypt($request->password);
        $instruktur = Instruktur::create($storeData);

        return response([
            'message' => 'Insturktur berhasil ditambahkan',
            'data' => $instruktur
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
        $instruktur = Instruktur::find($id);

        if (!is_null($instruktur)) {
            return response([
                'message' => 'Berhasil Mendapatkan Data',
                'data' => $instruktur
            ], 200);
        }
        return response([
            'message' => 'instruktur Tidak Ditemukan',
            'data' => null
        ], 404);
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
        $instruktur = Instruktur::find($id);
        if (is_null($instruktur)) {
            return response([
                'message' => 'instruktur Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required',
            'tanggal_lahir' => 'required|date',
            'no_telp' => 'required',
            'password' => 'string|min:8|unique:instruktur' ,
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 200);
        }

        $instruktur->nama = $updateData['nama'];
        $instruktur->email = $updateData['email'];
        $instruktur->alamat = $updateData['alamat'];
        $instruktur->tanggal_lahir = $updateData['tanggal_lahir'];
        $instruktur->no_telp = $updateData['no_telp'];

        if (isset($updateData['password'])) {
            $instruktur->password = bcrypt($updateData['password']);
        }


        if ($instruktur->save()) {
            return response([
                'message' => 'Insturktur Berhasil Diupdate',
                'data' => $instruktur
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $instruktur = Instruktur::find($id);

        if (is_null($instruktur)) {
            return response([
                'message' => 'instruktur Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        if ($instruktur->delete()) {
            return response([
                'message' => 'instruktur Berhasil Dihapus',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'instruktur Gagal Dihapus',
            'data' => null
        ], 400);
    }
}
