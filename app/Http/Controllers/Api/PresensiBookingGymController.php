<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\PresensiBookingGym;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Validator;

class PresensiBookingGymController extends Controller
{
    public function bookingGym(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'tanggal_yang_dibooking' => 'required',
            'slot_waktu' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }
        $member = Member::where('id', $request->user()->id)->first();
        if ($member->status == 'Inactive') {
            return response(['message' => 'Member tidak aktif'], 400);
        }

        $cekMemberGym = PresensiBookingGym::where('tanggal_yang_dibooking', $request->tanggal_yang_dibooking)
            ->where('slot_waktu', $request->slot_waktu)
            ->where('id_member', $request->user()->id)->count();

        if($cekMemberGym > 0){
            return response(['message' => 'Anda sudah booking gym pada sesi waktu ini'], 400);
        }

        $cekGym = PresensiBookingGym::where('tanggal_yang_dibooking', $request->tanggal_yang_dibooking)
            ->where('slot_waktu', $request->slot_waktu)->count();
            
        if ($cekGym >= 10) {
            return response(['message' => 'Gym Sudah Penuh'], 400);
        }


        $date = date("y.m.");
        $id = IdGenerator::generate(['table' => 'presensi_booking_gym', 'length' => 9, 'prefix' => $date]);
        $storeData['id_member'] = $request->user()->id;
        $storeData['id'] = $id;
        $storeData['tanggal_booking'] = date("Y-m-d");
        $bookingGym = PresensiBookingGym::create($storeData);
        return response([
            'message' => 'Booking Gym Berhasil',
            'data' => $bookingGym,
        ], 200);
    }

    public function showBookingGymMember(Request $request)
    {
        $bookingGym = PresensiBookingGym::with(['FMember'])->where('id_member', $request->user()->id)->get();

        if (count($bookingGym) > 0) {
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $bookingGym
            ], 200);
        }

        return response([
            'message' => 'Data tidak ada',
            'data' => null
        ], 404);
    }

    public function batalKelas($id){
        $bookingGym = PresensiBookingGym::where('id', $id)->first();
        if($bookingGym){
            if($bookingGym->tanggal_yang_dibooking <= date("Y-m-d")){
                return response([
                    'message' => 'Tidak Bisa Batal Booking Gym',
                ], 400);
            }

            $bookingGym->delete();
            return response([
                'message' => 'Berhasil membatalkan booking gym',
                'data' => $bookingGym
            ], 200);
        }

        return response([
            'message' => 'Data tidak ada',
            'data' => null
        ], 404);
    }

    public function getPresensiBookingGymToday(){
        $bookingGym = PresensiBookingGym::with(['FMember'])->where('tanggal_yang_dibooking', date("Y-m-d"))->get();

        if (count($bookingGym) > 0) {
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $bookingGym
            ], 200);
        }

        return response([
            'message' => 'Data tidak ada',
            'data' => null
        ], 404);
    }

    public function PresensiMember($id){
        $bookingGym = PresensiBookingGym::where('id', $id)->first();
        
        if($bookingGym->jam_presensi != null){
            return response([
                'message' => 'Presensi sudah dilakukan',
            ], 400);
        }
        $bookingGym->jam_presensi = date("Y-m-d H:i:s");
        $bookingGym->save();
        return response([
            'message' => 'Berhasil melakukan presensi',
            'data' => $bookingGym
        ], 200);
    }

    public function getPresensiBookingGymById($id){
        $bookingGym = PresensiBookingGym::with(['FMember'])->find($id);
        if($bookingGym){
            return response([
                'message' => 'Berhasil menerima data',
                'data' => $bookingGym
            ], 200);
        }
        return response([
            'message' => 'Data tidak ada',
            'data' => null
        ], 404);
    }
}
