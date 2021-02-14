<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Reservation;
use Illuminate\Support\Carbon;

class HomeController extends DashboardController{

    // get all contact us messages
    public function show(){
        $data['monthly_canceled_orders'] = Reservation::select('id', 'created_at')
        ->where('doctor_lawyer_id', auth()->user()->id)
        ->where('status', 5)
        ->orWhere('status', 6)
        ->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });

        $data['canceled_orders_count'] = [];
        $data['canceled_orders_arr'] = [];

        foreach ($data['monthly_canceled_orders'] as $key => $value) {
            $data['canceled_orders_count'][(int)$key] = count($value);
        }

        for($i = 1; $i <= 12; $i++){
            if(!empty($data['canceled_orders_count'][$i])){
                $data['canceled_orders_arr'][$i] = $data['canceled_orders_count'][$i];    
            }else{
                $data['canceled_orders_arr'][$i] = 0;    
            }
        }

        $data['monthly_completed_orders'] = Reservation::select('id', 'created_at')
        ->where('doctor_lawyer_id', auth()->user()->id)
        ->where('status', 2)
        ->orWhere('status', 3)
        ->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        $data['completed_orders_count'] = [];
        $data['completed_orders_arr'] = [];

        foreach ($data['monthly_completed_orders'] as $key => $value) {
            $data['completed_orders_count'][(int)$key] = count($value);
        }

        for($i = 1; $i <= 12; $i++){
            if(!empty($data['completed_orders_count'][$i])){
                $data['completed_orders_arr'][$i] = $data['completed_orders_count'][$i];    
            }else{
                $data['completed_orders_arr'][$i] = 0;    
            }
        }

        $data['monthly_Inprogress_orders'] = Reservation::select('id', 'created_at')
        ->where('doctor_lawyer_id', auth()->user()->id)
        ->where('status', 1)
        ->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        $data['Inprogress_orders_count'] = [];
        $data['Inprogress_orders_arr'] = [];

        foreach ($data['monthly_Inprogress_orders'] as $key => $value) {
            $data['Inprogress_orders_count'][(int)$key] = count($value);
        }

        for($i = 1; $i <= 12; $i++){
            if(!empty($data['Inprogress_orders_count'][$i])){
                $data['Inprogress_orders_arr'][$i] = $data['Inprogress_orders_count'][$i];    
            }else{
                $data['Inprogress_orders_arr'][$i] = 0;    
            }
        }
        $data['in_progress_orders'] = Reservation::where('doctor_lawyer_id', auth()->user()->id)->where('status', 1)->sum('cost');
        $data['canceled_orders'] = Reservation::where('doctor_lawyer_id', auth()->user()->id)->where('status', 5)->orWhere('status', 6)->sum('cost');
        $data['delivered_orders'] = Reservation::where('doctor_lawyer_id', auth()->user()->id)->where('status', 2)->orWhere('status', 3)->sum('cost');
        $data['total_value'] = (double)$data['in_progress_orders'] + (double)$data['canceled_orders'] + (double)$data['delivered_orders'];
        $data['delivered_orders_cost'] = Reservation::where('doctor_lawyer_id', auth()->user()->id)->where('status', 2)->orWhere('status', 3)->sum('cost');
        $data['recent_reservations'] = Reservation::where('doctor_lawyer_id', auth()->user()->id)->orderBy('created_at', 'desc')->take(7)->get();
        return view('dashboard.home' , ['data' => $data]);   
    }

}