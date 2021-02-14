<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Service;
use App\DoctorsLawyers;
use App\DoctorLawyerService;
use App\PlaceImage;
use App\TimesOfWork;

class DashboardController extends Controller{

    public function __construct()
    {
        $this->middleware('auth:dashboard', ['except' => ['getlogin' , 'postlogin']]);
    }

    // get login page
    public function getlogin(){
        return view('dashboard.login');
    }

    // post login 
    public function postlogin(Request $request){
        $credentials = request(['email', 'password']);

        if (Auth::guard('dashboard')->attempt($credentials)) {
            $user = Auth::guard('dashboard')->user();
            return redirect('/dashboard');
        } else {
            return view('dashboard.login');
        }
    }

    // logout
    public function logout(){
        $user = Auth::guard('dashboard')->user();
        Auth::logout();
        return redirect('/dashboard/login');
    }   
    
    // type : get => profile
    public function getProfile() {
        $data['drlaw'] = Auth::user();
        $data['categories'] = Category::where('deleted', 0)->get();
        $data['drlaw_services'] = $data['drlaw']->drLawServices()->pluck('service_id')->toArray();
        $data['services'] = Service::where('category_id', $data['drlaw']['category_id'])->get();

        return view('dashboard.profile', ['data' => $data]);
    }

    // type : post => profile
    public function postProfile(Request $request) {
        $user = Auth::user();
        $drlaw = DoctorsLawyers::find($user->id);
        $post = $request->except(['service_id']);

        if (isset($request->password) && !empty($request->password)) {
            $post['password'] = Hash::make($post['password']);
        }else {
            $post['password'] = $drlaw->password;
        }
        

        if ($request->file('personal_image') && !empty($request->file('personal_image'))) {
            $image_name = $request->file('personal_image')->getRealPath();
            Cloudder::upload($image_name, null);
            $imagereturned = Cloudder::getResult();
            $image_id = $imagereturned['public_id'];
            $image_format = $imagereturned['format'];    
            $image_new_name = $image_id.'.'.$image_format;
            $post['personal_image'] = $image_new_name;
        }else {
            $post['personal_image'] = $drlaw->personal_image;
        }

        if ($request->file('image_professional_title') && !empty($request->file('image_professional_title'))) {
            $image_name = $request->file('image_professional_title')->getRealPath();
            Cloudder::upload($image_name, null);
            $imagereturned = Cloudder::getResult();
            $image_id = $imagereturned['public_id'];
            $image_format = $imagereturned['format'];    
            $image_new_name = $image_id.'.'.$image_format;
            $post['image_professional_title'] = $image_new_name;
        }else {
            $post['image_professional_title'] = $drlaw->image_professional_title;
        }

        if ($request->file('image_profession_license') && !empty($request->file('image_profession_license'))) {
            $image_name = $request->file('image_profession_license')->getRealPath();
            Cloudder::upload($image_name, null);
            $imagereturned = Cloudder::getResult();
            $image_id = $imagereturned['public_id'];
            $image_format = $imagereturned['format'];    
            $image_new_name = $image_id.'.'.$image_format;
            $post['image_profession_license'] = $image_new_name;
        }else {
            $post['image_profession_license'] = $drlaw->image_profession_license;
        }

        $drlaw->update($post);
        if (isset($request->service_id) && !empty($request->service_id)) {
            $drlaw->drLawServices()->delete();
            for($i =0; $i < count($request->service_id); $i ++) {
                DoctorLawyerService::create([
                    'service_id' => $request->service_id[$i],
                    'doctor_lawyer_id' => $drlaw->id
                ]);
            }
        }

        return redirect()->back();
    }

    // type : get => location data
    public function getLocationData() {
        $data['drlaw'] = Auth::user();

        return view('dashboard.location_data', ['data' => $data]);
    }

    // type : post => location data
    public function postLocationData(Request $request) {
        $user = Auth::user();
        $drlaw = DoctorsLawyers::find($user->id);

        $post = $request->all();

        // dd($request->all());
        if (isset($post['location_link']) && !empty($post['location_link'])) {
            $url=$post['location_link'];
        
            preg_match('/@(\-?[0-9]+\.[0-9]+),(\-?[0-9]+\.[0-9]+)/', $url, $match );
            // dd($match);
            $post['longitude']=$match[2];
            $post['latitude']=$match[1];
        }else {
            $post['longitude']=$drlaw->longitude;
            $post['latitude']=$drlaw->latitude;
        }

        if ( $images = $request->file('place_image') ) {
            foreach($drlaw->placeImages as $placImage) {
                $image = $placImage->image;
                $publicId = substr($image, 0 ,strrpos($image, "."));    
                Cloudder::delete($publicId);
            }
            $drlaw->placeImages()->delete();
            foreach ($images as $image) {
                $image_name = $image->getRealPath();
                Cloudder::upload($image_name, null);
                $imagereturned = Cloudder::getResult();
                $image_id = $imagereturned['public_id'];
                $image_format = $imagereturned['format'];    
                $image_new_name = $image_id.'.'.$image_format;
                PlaceImage::create(["image" => $image_new_name, "doctor_lawyer_id" => $drlaw->id]);
            }
        }

        $drlaw->update($post);

        return redirect()->back();
    }

    // type : get => times of work
    public function getTimesOfWork() {
        $user = Auth::user();
        $data['drlaw'] = DoctorsLawyers::find($user->id);

        $data['sunday'] = 0;
        $data['monday'] = 0;
        $data['tuesday'] = 0;
        $data['wdnesday'] = 0;
        $data['thrusday'] = 0;
        $data['friday'] = 0;
        $data['saturday'] = 0;
        for ($i = 0; $i < count($data['drlaw']->times); $i ++) {
            if ($data['drlaw']->times[$i]->day == 0) {
                if ($data['drlaw']->times[$i]->holiday == 0) {
                    $data['sunday'] = 1;
                }
            }
            if ($data['drlaw']->times[$i]->day == 1) {
                if ($data['drlaw']->times[$i]->holiday == 0) {
                    $data['monday'] = 1;
                }
            }
            if ($data['drlaw']->times[$i]->day == 2) {
                if ($data['drlaw']->times[$i]->holiday == 0) {
                    $data['tuesday'] = 1;
                }
            }
            if ($data['drlaw']->times[$i]->day == 3) {
                if ($data['drlaw']->times[$i]->holiday == 0) {
                    $data['wdnesday'] = 1;
                }
            }
            if ($data['drlaw']->times[$i]->day == 4) {
                if ($data['drlaw']->times[$i]->holiday == 0) {
                    $data['thrusday'] = 1;
                }
            }
            if ($data['drlaw']->times[$i]->day == 5) {
                if ($data['drlaw']->times[$i]->holiday == 0) {
                    $data['friday'] = 1;
                }
            }
            if ($data['drlaw']->times[$i]->day == 6) {
                if ($data['drlaw']->times[$i]->holiday == 0) {
                    $data['saturday'] = 1;
                }
            }
        }

        return view('dashboard.times_of_work', ['data' => $data]);
    }

    // type : post => times of work
    public function postTimesOfWork(Request $request) {
        $user = Auth::user();
        $drlaw = DoctorsLawyers::find($user->id);
        $drlaw->times()->delete();
        if ($request->sunday_work) {
            
            if (count($request->sunday_from) > 0) {
                for ($i = 0; $i < count($request->sunday_from); $i ++) {
                    $workTime = [];
                    $sunday['day'] = 0;
                    $sunday['holiday'] = 0;
                    $sunday['from'] = $request->sunday_from[$i];
                    $sunday['to'] = $request->sunday_to[$i];
                    $sunday['doctor_lawyer_id'] = $drlaw->id;
                    if ($request->reservation_type == "attendance") {
                        $sunday['to'] = $request->sunday_count[$i];
                    }
                    TimesOfWork::create($sunday);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 0;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $drlaw->id;
            TimesOfWork::create($workTime);
        }

        if ($request->monday_work) {
            if (count($request->monday_from) > 0) {
                for ($i = 0; $i < count($request->monday_from); $i ++) {
                    $workTime = [];
                    $workTime['day'] = 1;
                    $workTime['holiday'] = 0;
                    $workTime['from'] = $request->monday_from[$i];
                    $workTime['to'] = $request->monday_to[$i];
                    $workTime['doctor_lawyer_id'] = $drlaw->id;
                    if ($request->reservation_type == "attendance") {
                        $workTime['to'] = $request->monday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 1;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $drlaw->id;
            TimesOfWork::create($workTime);
        }

        if ($request->tuesday_work) {
            
            if (count($request->tuesday_from) > 0) {
                for ($i = 0; $i < count($request->tuesday_from); $i ++) {
                    $workTime = [];
                    $workTime['day'] = 2;
                    $workTime['holiday'] = 0;
                    $workTime['from'] = $request->tuesday_from[$i];
                    $workTime['to'] = $request->tuesday_to[$i];
                    $workTime['doctor_lawyer_id'] = $drlaw->id;
                    if ($request->reservation_type == "attendance") {
                        $workTime['to'] = $request->tuesday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 2;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $drlaw->id;
            TimesOfWork::create($workTime);
        }

        if ($request->wednesday_work) {
            
            if (count($request->wednesday_from) > 0) {
                for ($i = 0; $i < count($request->wednesday_from); $i ++) {
                    $workTime = [];
                    $workTime['day'] = 3;
                    $workTime['holiday'] = 0;
                    $workTime['from'] = $request->wednesday_from[$i];
                    $workTime['to'] = $request->wednesday_to[$i];
                    $workTime['doctor_lawyer_id'] = $drlaw->id;
                    if ($request->reservation_type == "attendance") {
                        $workTime['to'] = $request->wednesday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 3;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $drlaw->id;
            TimesOfWork::create($workTime);
        }

        if ($request->thursday_work) {
            
            if (count($request->thursday_from) > 0) {
                for ($i = 0; $i < count($request->thursday_from); $i ++) {
                    $workTime = [];
                    $workTime['day'] = 4;
                    $workTime['holiday'] = 0;
                    $workTime['from'] = $request->thursday_from[$i];
                    $workTime['to'] = $request->thursday_to[$i];
                    $workTime['doctor_lawyer_id'] = $drlaw->id;
                    if ($request->reservation_type == "attendance") {
                        $workTime['to'] = $request->thursday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 4;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $drlaw->id;
            TimesOfWork::create($workTime);
        }

        if ($request->friday_work) {
            
            if (count($request->friday_from) > 0) {
                for ($i = 0; $i < count($request->friday_from); $i ++) {
                    $workTime = [];
                    $workTime['day'] = 5;
                    $workTime['holiday'] = 0;
                    $workTime['from'] = $request->friday_from[$i];
                    $workTime['to'] = $request->friday_to[$i];
                    $workTime['doctor_lawyer_id'] = $drlaw->id;
                    if ($request->reservation_type == "attendance") {
                        $workTime['to'] = $request->friday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 5;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $drlaw->id;
            TimesOfWork::create($workTime);
        }

        if ($request->saturday_work) {
            
            if (count($request->saturday_from) > 0) {
                for ($i = 0; $i < count($request->saturday_from); $i ++) {
                    $workTime = [];
                    $workTime['day'] = 6;
                    $workTime['holiday'] = 0;
                    $workTime['from'] = $request->saturday_from[$i];
                    $workTime['to'] = $request->saturday_to[$i];
                    $workTime['doctor_lawyer_id'] = $drlaw->id;
                    if ($request->reservation_type == "attendance") {
                        $workTime['to'] = $request->saturday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 6;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $drlaw->id;
            TimesOfWork::create($workTime);
        }

        return redirect()->back();
    }

}