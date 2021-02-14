<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Favorite;
use App\DoctorsLawyers;
use App\Category;
use App\DoctorLawyerService;
use App\Service;
use App\Rate;
use App\Holiday;
use App\TimesOfWork;
use App\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\APIHelpers;


class FavoriteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api' , ['except' => []]);
    }

    // get favorites
    public function getfavorites(Request $request){
        $user = auth()->user();
        $user_id = $user->id;
        $type = $request->type;

       $favorites = Favorite::where('user_id' , $user_id)->where('type' , $type)->get()->toArray();
       $ids = array_column($favorites, 'doctor_lawyer_id');
       
       $doctors_lawyers = DoctorsLawyers::
       whereIn('id' , $ids)
       ->select('id' , 'personal_image' , 'app_name_en' , 'gender' , 'app_name_ar' , 'professional_title_en' , 'professional_title_ar' , 'city_en' , 'city_ar' , 'reservation_cost' , 'category_id' , 'reservation_type' , 'latitude' , 'longitude')
       ->get();

       for($i = 0; $i < count($doctors_lawyers); $i++ ){
        
        // category
        $categoryid = $doctors_lawyers[$i]['category_id'];
        $category_row = Category::Where('id' , $categoryid)->first();
        $doctors_lawyers[$i]['category_name_en'] = $category_row['title_en'];
        $doctors_lawyers[$i]['category_name_ar'] = $category_row['title_ar'];
        
        // services
        $services = DoctorLawyerService::Where('doctor_lawyer_id' , $doctors_lawyers[$i]['id'])->get();
        for($j = 0; $j < count($services); $j++){
            $service = Service::select('title_en' , 'title_ar')->find($services[$j]['service_id']); 
            $array[$i][$j] = $service;
        }
        $doctors_lawyers[$i]['services'] = $array[$i]; 

        // favorite
        $doctors_lawyers[$i]['favorite'] = true;

        // rate
        $rates = Rate::where('doctor_lawyer_id' , $doctors_lawyers[$i]['id'])->where('admin_approval' , 1)->get();
        $doctors_lawyers[$i]['rate_count'] = count($rates);
        if($doctors_lawyers[$i]['rate_count'] == 0 ){
            $doctors_lawyers[$i]['rate_average'] = "0";
        }else{
            $sumrates = Rate::where('doctor_lawyer_id' , $doctors_lawyers[$i]['id'])->where('admin_approval' , 1)->sum('rate');
            $average = $sumrates / $doctors_lawyers[$i]['rate_count'];
            $averageFormat = number_format((float)$average, 1, '.', '');
            $doctors_lawyers[$i]['rate_average'] = $averageFormat;
        }

        // available today
        $current_date = Date('yy-m-d');
        $holiday = Holiday::where('doctor_lawyer_id' , $doctors_lawyers[$i]['id'])->where('date' , $current_date )->first();
        if($holiday){
            $doctors_lawyers[$i]['available_today'] = false;
        }else{
            $number = date('N', strtotime($current_date));
            $periods_of_today = TimesOfWork::where('day' , $number)->where('doctor_lawyer_id' , $doctors_lawyers[$i]['id'])->get();
            
            if(count($periods_of_today) == 0){
                $doctors_lawyers[$i]['available_today'] = false;
            }else{
                if($periods_of_today[0]['holiday'] == 1){
                    $doctors_lawyers[$i]['available_today'] = false;
                }else{
                    if($doctors_lawyers[$i]['reservation_type'] == 'attendance' ){

                        for($n = 0 ; $n < count($periods_of_today) ; $n++){
                            $max_reservation_count = $periods_of_today[$n]['count'];
                            $today_reservations = Reservation::where('work_time_id' , $periods_of_today[$n]['id'])->where('date' , $current_date)->get();
                            $count_today_reservations = count($today_reservations);
                            $filled_periods_array = array();
                            if($max_reservation_count == $count_today_reservations){
                                array_push($filled_periods_array , 'filed');
                            }else{
                                array_push($filled_periods_array , 'empty');
                            }
                        }

                        if (in_array("empty", $filled_periods_array)){
                            $doctors_lawyers[$i]['available_today'] = true;
                        }else{
                            $doctors_lawyers[$i]['available_today'] = false;
                        }
                    
                    }else{
                        for($n = 0 ; $n < count($periods_of_today) ; $n++){
                            $today_reservations = Reservation::where('work_time_id' , $periods_of_today[$n]['id'])->where('date' , $current_date)->first();
                            $filled_periods_array = array();
                            if($today_reservations){
                                array_push($filled_periods_array , 'filed');
                            }else{
                                array_push($filled_periods_array , 'empty');
                            }
                        }
                        

                        if (in_array("empty", $filled_periods_array)){
                            $doctors_lawyers[$i]['available_today'] = true;
                        }else{
                            $doctors_lawyers[$i]['available_today'] = false;
                        }
                    }
                }
            }   
        }   

    }

    $response = APIHelpers::createApiResponse(false , 200 , '' , '', $doctors_lawyers);
    return response()->json($response , 200);
       
    }

    public function addtofavorites(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'doctor_lawyer_id' => 'required',
        ]);

        if($validator->fails()){
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' ,'بعض الحقول مطلوبة' , null);
            return response()->json($response , 406);
        }

        $user_id = auth()->user()->id;
        $type = $request->type;
        $doctor_lawyer_id = $request->doctor_lawyer_id;

        $addedBefore = Favorite::where('user_id' , $user_id )->where('type', $type)->where('doctor_lawyer_id' , $doctor_lawyer_id )->first();
        if($addedBefore){
            $response = APIHelpers::createApiResponse(true , 400 , 'Added Before To Favorites' , 'تمت إضافتة من قبل إلي المفضله' , null);
            return response()->json($response , 400);
        }

        $favorite = new Favorite();
        $favorite->user_id = $user_id;
        $favorite->type = $type;
        $favorite->doctor_lawyer_id = $doctor_lawyer_id;
        $favorite->save();
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $favorite);
        return response()->json($response , 200);
    }

    public function removefromfavorites(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'doctor_lawyer_id' => 'required',
        ]);

        if($validator->fails()){
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

        $user_id = auth()->user()->id;
        $type = $request->type;
        $doctor_lawyer_id = $request->doctor_lawyer_id;

        $addedBefore = Favorite::where('user_id' , $user_id )->where('type', $type)->where('doctor_lawyer_id' , $doctor_lawyer_id )->first();

        if(!$addedBefore){
            $response = APIHelpers::createApiResponse(true , 400 , 'Not Added Before To Favorites' , 'لا يوجد في الفضلة' , null);
            return response()->json($response , 400);
        }

        Favorite::where('user_id' , $user_id)->where('type' , $type)->where('doctor_lawyer_id' , $doctor_lawyer_id)->delete();
        $response = APIHelpers::createApiResponse(false , 200 , 'Removed From Favorites Successfully' , 'تم حذفة من المفضله بنجاح' , []);
        return response()->json($response , 200); 
    }
}
