<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ContactUs;
use App\User;
use App\Reservation;
use App\DoctorsLawyers;
use Illuminate\Support\Carbon;

class HomeController extends AdminController{

    // get all contact us messages
    public function show(){
        $data['monthly_canceled_orders'] = Reservation::select('id', 'created_at')
        ->where('status', 5)
        ->orWhere('status', 6)
        ->get()
        ->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        // dd($data['monthly_canceled_orders']);
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
        // dd($data['Inprogress_orders_arr']);
        $data['users'] = User::count();
        $data['recent_reservations'] = Reservation::orderBy('created_at', 'desc')->take(7)->get();
        $data['most_dr_lawyer_reserved'] = DoctorsLawyers::join("reservations", "reservations.doctor_lawyer_id", "=", "doctors_lawyers.id")
        ->where("doctors_lawyers.active", 1)
        ->select('doctors_lawyers.app_name_en', 'doctors_lawyers.app_name_ar', 'doctors_lawyers.id', 'doctors_lawyers.type','doctors_lawyers.professional_title_en','doctors_lawyers.professional_title_ar', DB::raw('SUM(reservations.cost) as costSum'), DB::raw('COUNT(reservations.id) as cnt'))
        ->groupBy('doctors_lawyers.app_name_en')
        ->groupBy('doctors_lawyers.app_name_ar')
        ->groupBy('doctors_lawyers.professional_title_en')
        ->groupBy('doctors_lawyers.professional_title_ar')
        ->groupBy('doctors_lawyers.id')
        ->groupBy('doctors_lawyers.type')
        ->groupBy('reservations.cost')
        ->groupBy('reservations.id')
        ->orderBy('cnt', 'desc')
        ->take(7)
        ->get();
        $data['in_progress_orders'] = Reservation::where('status', 1)->sum('cost');
        $data['canceled_orders'] = Reservation::where('status', 5)->orWhere('status', 6)->sum('cost');
        $data['delivered_orders'] = Reservation::where('status', 2)->orWhere('status', 3)->sum('cost');
        $data['total_value'] = (double)$data['in_progress_orders'] + (double)$data['canceled_orders'] + (double)$data['delivered_orders'];
        $data['delivered_orders_cost'] = Reservation::where('status', 2)->orWhere('status', 3)->sum('cost');
        $data['contact_us'] = ContactUs::count();
        $data['reservations'] = Reservation::count();

        return view('admin.home' , ['data' => $data]);   
    }

}