<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Reservation;
use App\User;
use App\Rate;

class ReservationController extends DashboardController{
    // index
    public function show(Request $request) {
        $query = Reservation::with('doctorLawyer')->where('doctor_lawyer_id', auth()->user()->id);

        if (isset($request->status) && !empty($request->status)) {
            $data['status'] = $request->status;
            $query->where('status', $request->status);
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

        return view('dashboard.reservations', ['data' => $data]);
    }

    // details
    public function details(Reservation $reservation) {
        $data['reservation'] = $reservation;

        return view('dashboard.reservation_details', ['data' => $data]);
    }

    // get all rates
    public function Getrates(Request $request){
        $data['rates'] = Rate::where('doctor_lawyer_id', auth()->user()->id)->orderBy('id' , 'desc')->get();
        for($i = 0 ; $i < count($data['rates']); $i++){
            $data['rates'][$i]['user'] = User::select('id' , 'name')->find($data['rates'][$i]['user_id']);
        }
        return view('dashboard.rates' , ['data' => $data]);
    }
}