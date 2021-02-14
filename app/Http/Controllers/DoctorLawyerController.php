<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\DoctorsLawyers;
use App\Category;
use App\Rate;
use App\Favorite;
use App\Service;
use App\DoctorLawyerService;
use App\Holiday;
use App\TimesOfWork;
use App\Reservation;
use App\PlaceImage;
use Illuminate\Support\Facades\DB;
use App\User;


class DoctorLawyerController extends Controller
{

    // get nearby doctors or lawyers
    public function nearby(Request $request)
    {
        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $category_id = $request->query('category_id');
        $page = $request->query('page'); // used for pagination starts from 1
        $type = $request->type;
        $sort = $request->query('sort');

        if(! $latitude || ! $longitude || ! $category_id || ! $page){
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

        $doctors_lawyers = DoctorsLawyers::
                                Where('category_id' , $category_id)
                                ->Where('type' , $type)
                                ->where('active' , 1)
                                ->where('profile_completed' , 3)
                                ->orderBy(DB::raw("3959 * acos( cos( radians({$latitude}) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(-{$longitude}) ) + sin( radians({$latitude}) ) * sin(radians(latitude)) )"), 'ASC')
                                ->select('id' , 'personal_image AS image' , 'app_name_en AS name_en' , 'gender' , 'app_name_ar AS name_ar' , 'professional_title_en' , 'professional_title_ar' , 'city_en' , 'city_ar' , 'reservation_cost' , 'category_id' , 'reservation_type' , 'latitude' , 'longitude')
                                ->simplePaginate(50);

        for($i = 0; $i < count($doctors_lawyers); $i++ ){
            // $doctors_lawyers[$i]['distance'] = APIHelpers::distance($latitude , $longitude , $doctors_lawyers[$i]['latitude'] , $doctors_lawyers[$i]['longitude'] , 'K');
            
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
            if(auth()->user()){
                $favorite = Favorite::where('user_id' , auth()->user()->id)->where('doctor_lawyer_id' , $doctors_lawyers[$i]['id'])->first();
                if($favorite){
                    $doctors_lawyers[$i]['favorite'] = true;    
                }else{
                    $doctors_lawyers[$i]['favorite'] = false;
                }
            }else{
                $doctors_lawyers[$i]['favorite'] = false;
            }

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

        if($sort == 1){
            $data = $doctors_lawyers->sortBy('reservation_cost')->toArray();
            $data = array_values($data);
            for($i = 0 ; $i < count($doctors_lawyers); $i++){
                $doctors_lawyers[$i] = $data[$i];
            }
        }

        if($sort == 2){
            $data = $doctors_lawyers->sortByDesc('reservation_cost')->toArray();
            $data = array_values($data);
            for($i = 0 ; $i < count($doctors_lawyers); $i++){
                $doctors_lawyers[$i] = $data[$i];
            }
        }

        if($sort == 3){
            $data = $doctors_lawyers->sortByDesc('available_today')->toArray();
            $data = array_values($data);
            for($i = 0 ; $i < count($doctors_lawyers); $i++){
                $doctors_lawyers[$i] = $data[$i];
            }
        }

        if($sort == 4){
            $data = $doctors_lawyers->sortByDesc('rate_average')->toArray();
            $data = array_values($data);
            for($i = 0 ; $i < count($doctors_lawyers); $i++){
                $doctors_lawyers[$i] = $data[$i];
            }
        }


        
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $doctors_lawyers);
        return response()->json($response , 200);                
    }

    // get doctor or lawyer profile
    public function getprofile(Request $request){
        $doctor_lawyer_id = $request->id;
        $returned_doctor_lawyer = DoctorsLawyers::
        select('id' , 'personal_image' , 'app_name_en' , 'app_name_ar' , 'gender' , 'professional_title_en' , 'professional_title_ar' , 'city_en' , 'city_ar' , 'address_en' , 'address_ar' , 'reservation_cost' , 'reservation_type' , 'about_en' ,  'about_ar' , 'category_id')
        ->find($doctor_lawyer_id);
        
        $doctor_lawyer = $returned_doctor_lawyer;

        // favorite
        if(auth()->user()){
            $favorite = Favorite::where('user_id' , auth()->user()->id)->where('doctor_lawyer_id' , $doctor_lawyer_id)->first();
            if($favorite){
                $doctor_lawyer['favorite'] = true;    
            }else{
                $doctor_lawyer['favorite'] = false;
            }
        }else{
            $doctor_lawyer['favorite'] = false;
        }

        // category
        $category_row = Category::Where('id' , $returned_doctor_lawyer['category_id'])->first();
        $doctor_lawyer['vector'] = $category_row['vector'];

        // images
        $doctor_lawyer['images'] = PlaceImage::where('doctor_lawyer_id' , $doctor_lawyer_id)->get();

        // rates
        $rates = Rate::where('doctor_lawyer_id' , $doctor_lawyer_id)->where('admin_approval' , 1)->get()->toArray();
        $doctor_lawyer['rate_count'] = count($rates);
        if($doctor_lawyer['rate_count'] == 0 ){
            $doctor_lawyer['rate_average'] = "0";
        }else{
            $sumrates = Rate::where('doctor_lawyer_id' , $doctor_lawyer_id)->where('admin_approval' , 1)->sum('rate');
            $average = $sumrates / $doctor_lawyer['rate_count'];
            $averageFormat = number_format((float)$average, 1, '.', '');
            $doctor_lawyer['rate_average'] = $averageFormat;
        }

        $threerates = array_slice($rates ,0, 3);
        $doctor_lawyer['rates'] = $threerates;

        $rate = [];
        $rate = $doctor_lawyer['rates'];

        for($i = 0 ; $i < count($doctor_lawyer['rates']) ; $i++){
            $name = User::select('name')->find($doctor_lawyer['rates'][$i]['user_id']);
            $user_name =  $name['name'];
            $rate[$i]['user_name'] = $user_name;
        }

        $doctor_lawyer['rates'] = $rate;
  
        // availabilty today
        $current_date = Date('yy-m-d');
        $holiday = Holiday::where('doctor_lawyer_id' , $doctor_lawyer_id)->where('date' , $current_date )->first();
        if($holiday){
            $doctor_lawyer['available_today'] = false;
        }else{
            $number = date('N', strtotime($current_date));
            $periods_of_today = TimesOfWork::where('day' , $number)->where('doctor_lawyer_id' , $doctor_lawyer_id)->get();
            if(count($periods_of_today) == 0){
               $doctor_lawyer['available_today'] = false;
           }else{
               if($periods_of_today[0]['holiday'] == 1){
                    $doctor_lawyer['available_today'] = false;
                }else{
                    // attendance
                   if($doctor_lawyer['reservation_type'] == 'attendance' ){

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
                            $doctor_lawyer['available_today'] = true;
                        }else{
                           $doctor_lawyer['available_today'] = false;
                        }
                    
                  	 }else{
                        for($n = 0 ; $n < count($periods_of_today) ; $n++){
                            $today_reservations = Reservation::where('work_time_id' , $periods_of_today[$n]['id'])->where('date' , $current_date)->first();
                         //   echo $today_reservations;
                            $filled_periods_array = array();
                            if($today_reservations){
                                array_push($filled_periods_array , 'filed');
                            }else{
                                array_push($filled_periods_array , 'empty');
                            }
                        }
                        

                        if (in_array("empty", $filled_periods_array)){
                            $doctor_lawyer['available_today'] = true;
                        }else{
                            $doctor_lawyer['available_today'] = false;
                        }
                    }
               }
            }
        }

        // services
        $services = DoctorLawyerService::where('doctor_lawyer_id' , $doctor_lawyer_id)->get();
        if(count($services) > 0 ){
            for($j = 0; $j < count($services); $j++){
                $service = Service::select('title_en' , 'title_ar')->find($services[$j]['service_id']); 
                $array[$j] = $service;
            }
            $doctor_lawyer['services'] = $array; 
        }else{
            $doctor_lawyer['services'] = [];
        }
        
        // times of work
        $current_date = Date('yy-m-d');
        $number = date('N', strtotime($current_date));
        $doctor_lawyer['times_of_work'] = TimesOfWork::where('day' , $number)->where('doctor_lawyer_id' , $doctor_lawyer_id)->get();;

        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $doctor_lawyer);
        return response()->json($response , 200);  
    }


    // get times of work
    public function gettimesofwork(Request $request){
        $doctor_lawyer_id = $request->id;
        $current_date = Date('yy-m-d');
        $number = date('N', strtotime($current_date));

        $weekdays = [
            ["name_en" => "Monday" , "name_ar" => "الإثنين"] , 
            ["name_en" => "Tuesday" , "name_ar" => "الثلاثاء"] , 
            ["name_en" => "Wednesday" , "name_ar" => "الأربعاء"] , 
            ["name_en" => "Thursday" , "name_ar" => "الخميس"] , 
            ["name_en" => "Friday" , "name_ar" => "الجمعه"] , 
            ["name_en" => "Saturday" , "name_ar" => "السبت"] , 
            ["name_en" => "Sunday" , "name_ar" => "الأحد"]
        ];

        $doctor_lawyer = DoctorsLawyers::where('id' , $doctor_lawyer_id )->select('reservation_type')->first();
        $reservationType = $doctor_lawyer['reservation_type'];
        $data['reservation_type'] = $reservationType;
        $thirtydays = array();
        for($i = 0; $i < 30; $i++){
            $thirtydays[$i]['date'] = date("yy-m-d", strtotime('+'. $i .' days'));
            $thirtydays[$i]['number'] = date('N', strtotime($thirtydays[$i]['date']));
            $thirtydays[$i]['day'] = $weekdays[$thirtydays[$i]['number']-1];
            $holiday = Holiday::where('doctor_lawyer_id' , $doctor_lawyer_id)->where('date' , $thirtydays[$i]['date'] )->first();
            if($holiday){
                $thirtydays[$i]['available'] = false;
                $thirtydays[$i]['timesofwork'] = [];
            }else{
                $periods_of_day = TimesOfWork::where('day' , $thirtydays[$i]['number'])->where('doctor_lawyer_id' , $doctor_lawyer_id)->get();
                $thirtydays[$i]['timesofwork'] = $periods_of_day;
                if(count($periods_of_day) == 0){
                    $thirtydays[$i]['available'] = false;
                }else{
                    if($periods_of_day[0]['holiday'] == 1){
                        $thirtydays[$i]['available'] = false;
                    }else{
                        if($reservationType == 'attendance'){
                            for($n = 0 ; $n < count($periods_of_day) ; $n++){
								$thirtydays[$i]['timesofwork'][$n]['date'] = $thirtydays[$i]['date'];
                                $max_reservation_count = $periods_of_day[$n]['count'];
                                $day_reservations = Reservation::where('work_time_id' , $periods_of_day[$n]['id'])->where('date' , $thirtydays[$i]['date'])->get();
                                $count_day_reservations = count($day_reservations);
                                $filled_periods_array = array();
                                if($max_reservation_count == $count_day_reservations){
                                    $thirtydays[$i]['timesofwork'][$n]['avialable'] = false;
                                    array_push($filled_periods_array , 'filed');
                                }else{
                                    $thirtydays[$i]['timesofwork'][$n]['avialable'] = true;
                                    array_push($filled_periods_array , 'empty');
                                }

                                if (in_array("empty", $filled_periods_array)){
                                    $thirtydays[$i]['available'] = true;
                                }else{
                                    $thirtydays[$i]['available'] = false;
                                }

                            }
                        }else{
                            for($n = 0 ; $n < count($periods_of_day) ; $n++){
								$thirtydays[$i]['timesofwork'][$n]['date'] = $thirtydays[$i]['date'];
                                $day_reservations = Reservation::where('work_time_id' , $periods_of_day[$n]['id'])->where('date' , $thirtydays[$i]['date'])->first();
                                $filled_periods_array = array();
                                if($day_reservations){
                                    $thirtydays[$i]['timesofwork'][$n]['avialable'] = false;
                                    array_push($filled_periods_array , 'filed');
                                }else{
                                    $thirtydays[$i]['timesofwork'][$n]['avialable'] = true;
                                    array_push($filled_periods_array , 'empty');
                                }
                            }
                            

                            if (in_array("empty", $filled_periods_array)){
                                $thirtydays[$i]['available'] = true;
                            }else{
                                $thirtydays[$i]['available'] = false;
                            }
                        }
                    }
                }
            }
        }
        $data['thirtydays'] = $thirtydays;
        $response = APIHelpers::createApiResponse(false , 200 , '', '' , $data);
        return response()->json($response , 200);
    }

}
