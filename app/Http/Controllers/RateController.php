<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\APIHelpers;
use App\Rate;
use App\User;
use App\Reservation;


class RateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api' , ['except' => ['getrates']]);
    }

    public function getrates($type , $doctor_lawyer_id){
        $rates = Rate::where('doctor_lawyer_id' , $doctor_lawyer_id)->where('admin_approval' , 1)->select('id','rate' , 'text' , 'user_id')->get();
        $data['rates'] = $rates;
        $data['count'] = count($rates);
        $sumrates = Rate::where('doctor_lawyer_id' , $doctor_lawyer_id)->where('admin_approval' , 1)->sum('rate');
        if($data['count'] > 0 ){
            $average = $sumrates / $data['count'];
        }else{
            $average = "0";
        }
        
        
        $averageFormat = number_format((float)$average, 1, '.', '');
        $data['average'] = $averageFormat;

        for($i = 0; $i < count($data['rates']); $i++){
            $user = User::where('id' ,$data['rates'][$i]['user_id'])->select('name')->first();
            $data['rates'][$i]['user_name'] = $user['name'];
        }

        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $data);
        return response()->json($response , 200);
    }

    // rate doctor or lawyer
    public function addrate(Request $request){
        $validator = Validator::make($request->all(), [
            'rate' => 'required',
            'text' => 'required',
            'reservation_id' => 'required',
            'doctor_lawyer_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة'  , null);
            return response()->json($response , 406);
        }

        $user_id = auth()->user()->id;
        $reservation_id = $request->reservation_id;
        $doctor_lawyer_id = $request->doctor_lawyer_id;
        $rate = $request->rate;
        $text = $request->text;

        if($rate > 5){
            $response = APIHelpers::createApiResponse(true , 400 , 'Rate Must Be 5 Or Smaller' , 'التقييم يجب ان يكون ٥ او اقل' , null);
            return response()->json($response , 400);
        }

        $ratedBefore = Rate::where('user_id' , $user_id)->where('doctor_lawyer_id' , $doctor_lawyer_id)->where('reservation_id' , $reservation_id)->first();
        if($ratedBefore){
            $response = APIHelpers::createApiResponse(true , 400 , 'You Rated This Order Before' , 'لقد قييمت هذا الحجز مسبقا' , null);
            return response()->json($response , 400);
        }

        $reservation = Reservation::where('id' ,$reservation_id)->where('user_id' , $user_id)->first();
        if(! $reservation){
            $response = APIHelpers::createApiResponse(true , 400 , 'No Reservation Founded' , 'لم نجد الحجز' , null);
            return response()->json($response , 400);
        }

        if(in_array($reservation['status'], [1,3,4,5,6])){
            $response = APIHelpers::createApiResponse(true , 400 , 'You can not rate this reservation now' , 'لا يمكنك تقييم هذا الحجز الان' , null);
            return response()->json($response , 400);
        }

        $rate = new Rate();
        $rate->user_id = $user_id;
        $rate->doctor_lawyer_id = $doctor_lawyer_id;
        $rate->reservation_id = $reservation_id;
        $rate->rate = $rate;
        $rate->text = $text;

        $rate->save();

        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $rate);
        return response()->json($response , 200);
    }

}
