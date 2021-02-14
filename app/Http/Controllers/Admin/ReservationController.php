<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\DB;
use App\Reservation;
use App\DoctorsLawyers;

class ReservationController extends AdminController{
    // index
    public function show(Request $request) {
        $data['doctors_lawyers'] = DoctorsLawyers::where('active', 1)->get();
        $query = Reservation::with('doctorLawyer');
        
        if (isset($request->dr_lawyer) && !empty($request->dr_lawyer)) {
            $data['dr_law'] = $request->dr_lawyer;
            $query->where('doctor_lawyer_id', $request->dr_lawyer);
        }
        if (isset($request->status) && !empty($request->status)) {
            $data['status'] = $request->status;
            $query->where('status', $request->status);
        }
        if (isset($request->type) && !empty($request->type)) {
            $data['type'] = $request->type;
            $query->where('type', $request->type);
        }
        if (isset($request->user_name) && !empty($request->user_name)) {
            $data['username'] = $request->user_name;
            $query->where('user_name', 'like', '%' . $request->user_name . '%');
        }
        if (isset($request->from) && !empty($request->from)) {
            $data['from'] = $request->from;
            $data['to'] = $request->to;
            $query->whereBetween('date', array($request->from, $request->to));
        }
        
        $data['reservations'] = $query->get();
        
        

        return view('admin.reservations', ['data' => $data]);
    }

    // details
    public function details(Reservation $reservation) {
        $data['reservation'] = $reservation;

        return view('admin.reservation_details', ['data' => $data]);
    }
}