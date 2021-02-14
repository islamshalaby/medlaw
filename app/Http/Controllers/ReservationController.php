<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\APIHelpers;
use App\Holiday;
use App\DoctorsLawyers;
use App\Reservation;
use App\TimesOfWork;
use App\Rate;



class ReservationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api' , ['except' => []]);
    }

    // create reservation
    public function create(Request $request){

        // validation for required fields
        $validator = Validator::make($request->all() , [
            'doctor_lawyer_id' => 'required',
            'reservation_for' => 'required', // accountowner or someoneelse
            'date' => 'required',
            'work_time_id' => 'required',
            'user_name' => 'required',
            'phone' => 'required',
            'payment_mehod' => 'required'
        ]);

        if($validator->fails()){
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

        $user = auth()->user();

        // check if the payment_method valid string
        $payment_methods_strings = ['online' , 'cash' , 'wallet'];
        if(!in_array($request->payment_mehod, $payment_methods_strings)){
            $response = APIHelpers::createApiResponse(true , 400 , 'Invalid Payment type' , 'طريقة دفع غير متوفرة' , null);
            return response()->json($response , 400);
        }

        $doctor_lawyer = DoctorsLawyers::find($request->doctor_lawyer_id);

        if(!$doctor_lawyer){
            $response = APIHelpers::createApiResponse(true , 400 , 'Doctor or Lawyer Id Not found' , 'رقم طبيب او محامي غير صحيح' , null);
            return response()->json($response , 400);
        }

        // make sure that the doctor or the lawyer complete his profile and have an active account 
        if($doctor_lawyer['active'] == 0 || $doctor_lawyer['profile_completed'] < 3){
            $response = APIHelpers::createApiResponse(true , 400 , 'The reservation was not completed, please reserve someone else' , 'لم يكتمل الحجز من فضلك احجز لدي شخص اخر' , null);
            return response()->json($response , 400);
        }

        // make sure the day not holiday
        $holiday = Holiday::where('date' , $request->date)->where('doctor_lawyer_id' , $request->doctor_lawyer_id)->first();
        if($holiday){
            $response = APIHelpers::createApiResponse(true , 400 , 'You cannot book on a day off', 'لا يمكنك حجز يوم عطلة ' , null);
            return response()->json($response , 400);
        }

        $number = date('N', strtotime($request->date));

        // make sure the time is available
        $reservation_time = TimesOfWork::where('day' , $number)->find($request->work_time_id);

        // the time not exists
        if(!$reservation_time){
            $response = APIHelpers::createApiResponse(true , 400 , 'This time not available right now' , 'عفوا هذا الوقت لم يعد متاحا الان' , null);
            return response()->json($response , 400);
        }

        // day of
        if($reservation_time['holiday'] == 1){
            $response = APIHelpers::createApiResponse(true , 400 , 'This time not available right now' , ' عفوا هذا الوقت لم يعد متاحا الان' , null);
            return response()->json($response , 400);
        }

        // if doctor or lawyer reservation type attendance
        if($doctor_lawyer['reservation_type'] == "attendance"){
            // get the reservation in the requested day and time
            $day_reservations = Reservation::where('work_time_id' , $request->work_time_id)->where('date' , $request->date)->get();
            $count_day_reservations = count($day_reservations);
            $max_reservation_count = $reservation_time['count'];
            if($count_day_reservations == $max_reservation_count){
                $response = APIHelpers::createApiResponse(true , 400 , 'This time not available right now' , 'عفوا هذا الوقت لم يعد متاحا الان' , null);
                return response()->json($response , 400);
            }
        }else{
            // if doctor or lawyer reservation type intime
            $day_reservation = Reservation::where('work_time_id' , $request->work_time_id)->where('date' , $request->date)->first();
           if($day_reservation){
                $response = APIHelpers::createApiResponse(true , 400 , 'This time not available right now' , 'عفوا هذا الوقت لم يعد متاحا الان' , null);
                return response()->json($response , 400);
           } 
        }


        // check the payment method 
        // wallet
        // check if the balance is enough to complete the reservation
        if($request->payment_mehod == "wallet"){
            $wallet_balance = $user['wallet'];
            if($wallet_balance <  $doctor_lawyer['reservation_cost']){
                $response = APIHelpers::createApiResponse(true , 400 , 'The wallet balance is less than the reservation cost' , 'عفوا رصيد المحفظة اقل من تكلفة الحجز برجاء شحن المحفظة اولا' , null);
                return response()->json($response , 400);
            }else{
                $new_wallet_balance = $wallet_balance - $doctor_lawyer['reservation_cost'];
                $user->wallet = $new_wallet_balance;
                $user->save();
            }
        }

        $reservation = new Reservation();
        $reservation->date = $request->date;
        $reservation->type = $request->type;
        $reservation->time = $reservation_time['from'];
        $reservation->status = 1;
        $reservation->payment_method = $request->payment_mehod;
        $reservation->user_name = $request->user_name;
        $reservation->phone = $request->phone;
        $reservation->user_id = $user['id'];
        $reservation->doctor_lawyer_id = $request->doctor_lawyer_id;
        $reservation->work_time_id = $request->work_time_id;
        $reservation->cost = $doctor_lawyer['reservation_cost'];
        $reservation->reservation_for = $request->reservation_for;
        $reservation->latitude = $doctor_lawyer['latitude'];
        $reservation->longitude = $doctor_lawyer['longitude'];
        $reservation->address_ar = $doctor_lawyer['address_ar'];
        $reservation->address_en = $doctor_lawyer['address_en'];
        $reservation->city_en = $doctor_lawyer['city_en'];
        $reservation->city_ar = $doctor_lawyer['city_ar'];
        $reservation->save();
        $reservation['image'] =  $doctor_lawyer['personal_image'];
        $reservation['name_en'] =  $doctor_lawyer['app_name_en'];
        $reservation['name_ar'] =  $doctor_lawyer['app_name_ar'];
        $reservation['professional_title_en'] =  $doctor_lawyer['professional_title_en'];
        $reservation['professional_title_ar'] =  $doctor_lawyer['professional_title_ar'];
        $reservation['phone'] =  $doctor_lawyer['recieving_reservation_phone'];       
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $reservation);
        return response()->json($response , 200);
    }

    // get reservation details
    public function getreservationdetails(Request $request){
        $user = auth()->user();
        $reservation = Reservation::find($request->id);
        
        // check if the id of the reservation valid or not
        if(!$reservation){
            $response = APIHelpers::createApiResponse(true , 400 , 'Given Id Not Exist' , 'رقم الحجز غير موجود' , null);
            return response()->json($response , 400);            
        }

        // check user permissions to get the details of this reservation 
        if($user['id'] != $reservation['user_id']){
            $response = APIHelpers::createApiResponse(true , 400 , 'You do not have permission to view the details of this reservation' , 'ليس لديك الصلاحية لعرض تفاصيل هذا الحجز' , null);
            return response()->json($response , 400);
        }

        // get the doctor or the lawyer of this reservation
        $doctor_lawyer = DoctorsLawyers::find($reservation['doctor_lawyer_id']);

        $returnedreservation['image'] = $doctor_lawyer['personal_image'];
        $returnedreservation['name_en'] = $doctor_lawyer['app_name_en']; 
        $returnedreservation['name_ar'] = $doctor_lawyer['app_name_ar'];
        $returnedreservation['phone'] = $doctor_lawyer['recieving_reservation_phone'];
        $returnedreservation['professional_title_en'] = $doctor_lawyer['professional_title_en'];
        $returnedreservation['professional_title_ar'] = $doctor_lawyer['professional_title_ar'];
        $returnedreservation['city_en'] = $reservation['city_en'];
        $returnedreservation['city_ar'] = $reservation['city_ar'];       
        $returnedreservation['address_en'] = $reservation['address_en'];
        $returnedreservation['address_ar'] = $reservation['address_ar'];
        $returnedreservation['latitude'] = $reservation['latitude'];
        $returnedreservation['longitude'] = $reservation['longitude'];
        $returnedreservation['date'] = $reservation['date'];
        $returnedreservation['time'] = $reservation['time'];
        $returnedreservation['cost'] = $reservation['cost'];
        $returnedreservation['payment_method'] = $reservation['payment_method'];
        $returnedreservation['status'] = $reservation['status'];
        $returnedreservation['user_confirm'] = $reservation['user_confirm'];

        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $returnedreservation);
        return response()->json($response , 200);        
    }

    // confirm attendance
    public function confirmattendance(Request $request){
        $validator = Validator::make($request->all() , [
            'attendance' => 'required'
        ]);

        if($validator->fails()){
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

        $user_id = auth()->user()['id'];
        $reservation = Reservation::find($request->id);

        // check if the id of the reservation valid or not
        if(!$reservation){
            $response = APIHelpers::createApiResponse(true , 400 , 'Given Id Not Exist' , 'رقم الحجز غير موجود' , null);
            return response()->json($response , 400);            
        }

        // check user permissions to get the details of this reservation 
        if($user_id != $reservation['user_id']){
            $response = APIHelpers::createApiResponse(true , 400 , 'You do not have permission to update this reservation' , 'ليس لديك الصلاحية لتاكيد حضور هذا الحجز' , null);
            return response()->json($response , 400);
        }

        $reservation['user_confirm'] = $request->attendance;
        $reservation->save();

        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , []);
        return response()->json($response , 200);  
    }

    // get history
    public function gethistory(Request $request){
        $user_id = auth()->user()['id'];
        $type = $request->type;
        $reservations = Reservation::where('user_id' , $user_id)->where('type' , $type)->orderBy('date' , 'desc')->orderBy('time' , 'desc')->get();
        $returnedreservations = [];
        for($i =0 ; $i < count($reservations) ; $i++){
            $returnedreservations[$i]['id'] = $reservations[$i]['id'];
            $returnedreservations[$i]['date'] = $reservations[$i]['date'];
            $returnedreservations[$i]['time'] = $reservations[$i]['time'];
            $returnedreservations[$i]['latitude'] = $reservations[$i]['latitude'];
            $returnedreservations[$i]['longitude'] = $reservations[$i]['longitude'];
            $returnedreservations[$i]['status'] = $reservations[$i]['status'];
            $returnedreservations[$i]['user_confirm'] = $reservations[$i]['user_confirm'];
            $doctor_lawyer = DoctorsLawyers::select('app_name_en' , 'app_name_ar' , 'professional_title_en' , 'professional_title_ar', 'personal_image' , 'recieving_reservation_phone' )->find($reservations[$i]['doctor_lawyer_id']);
			$returnedreservations[$i]['doctor_lawyer_id'] = $reservations[$i]['doctor_lawyer_id'];
            $returnedreservations[$i]['name_en'] = $doctor_lawyer['app_name_en'];
            $returnedreservations[$i]['name_ar'] = $doctor_lawyer['app_name_ar'];
            $returnedreservations[$i]['professional_title_en'] = $doctor_lawyer['professional_title_en'];
            $returnedreservations[$i]['professional_title_ar'] = $doctor_lawyer['professional_title_ar'];
            $returnedreservations[$i]['image'] = $doctor_lawyer['personal_image'];
            $returnedreservations[$i]['phone'] = $doctor_lawyer['recieving_reservation_phone'];            
            if($reservations[$i]['status'] == 3 ){
                $rate = Rate::where('reservation_id' , $reservations[$i]['id'])->where('doctor_lawyer_id' , $reservations[$i]['doctor_lawyer_id'])->first();
                if($rate){
                    $returnedreservations[$i]['rate_count'] = $rate['rate'];
                    $returnedreservations[$i]['rate_text'] = $rate['text'];
                    $returnedreservations[$i]['rate_admin_approval'] = $rate['admin_approval'];                                        
                }
            }
        }
        
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $returnedreservations);
        return response()->json($response , 200); 

    }

    // cancel reservation
    public function cancelreservation(Request $request){
        $reservation_id = $request->id;
        $user_id = auth()->user()['id'];
        $reservation = Reservation::find($reservation_id);

        // check if this reservation exists or not
        if(!$reservation){
            $response = APIHelpers::createApiResponse(true , 400 , 'Given Id Not Exist' , 'رقم الحجز غير موجود' , null);
            return response()->json($response , 400);   
        }

        // check user permissions to get the details of this reservation 
        if($user_id != $reservation['user_id']){
            $response = APIHelpers::createApiResponse(true , 400 , 'You do not have permission to cancel this reservation' , 'ليس لديك الصلاحية لإلغاء هذا الحجز' , null);
            return response()->json($response , 400);
        }

        // check the availabilty for cancelling this reservation
        if(in_array($reservation['status'], [2,3,4,6])){
            $response = APIHelpers::createApiResponse(true , 400 , 'You can not cancell this reservation now' , 'لا يمكنك إلغاء هذا الحجز الان' , null);
            return response()->json($response , 400);
        }

        if($request->reason){
            $reservation['user_cancell_reason'] = $request->reason;    
        }
        
        $reservation['status'] = 5;
        // save 
        $reservation->save();
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , []);
        return response()->json($response , 200);  
    }
}
