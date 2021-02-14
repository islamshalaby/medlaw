<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\DoctorsLawyers;
use App\Category;
use App\Service;
use App\DoctorLawyerService;
use App\TimesOfWork;
use App\PlaceImage;

class DoctorLawyerController extends AdminController{
    // index
    public function show() {
        $data['doctorslawyers'] = DoctorsLawyers::get();

        return view('admin.doctorslawyers', ['data' => $data]);
    }

    // type : get => add
    public function AddGet() {
        $data['categories'] = Category::where('deleted', 0)->get();

        return view('admin.doctorlawyer_form', ['data' => $data]);
    }

    // type : get => fetchservicesbycategoryid
    public function fetchServicesByCategoryId(Category $category) {
        $rows = Service::where('category_id', $category->id)->get();
        $data = json_decode(($rows));


        return response($data, 200);
    }

    // type : post => add
    public function AddPost(Request $request) {
        $request->validate([
            'personal_image' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'app_name_en' => 'required',
            'app_name_ar' => 'required',
            'password' => 'required',
            'type' => 'required',
            'category_id' => 'required',
            'email' => 'required',
            'phone' => 'unique:doctors_lawyers,phone|required',
            'gender' => 'required',
            'image_professional_title' => 'required',
            'image_profession_license' => 'required',
            'professional_title_en' => 'required',
            'professional_title_ar' => 'required',
            'service_id' => 'required',
            'about_en' => 'required',
            'about_ar' => 'required',
            'city_en' => 'required',
            'city_ar' => 'required',
            'address_en' => 'required',
            'address_ar' => 'required',
            'location_link' => 'required',
            'reservation_type' => 'required',
            'recieving_reservation_phone' => 'unique:doctors_lawyers,recieving_reservation_phone|required',
            'reservation_cost' => 'required'
        ]);

        $post = $request->except(['service_id']);

        $post['password'] = Hash::make($post['password']);

        $url=$post['location_link'];
        
        preg_match('/@(\-?[0-9]+\.[0-9]+),(\-?[0-9]+\.[0-9]+)/', $url, $match );
        // dd($match);
        $post['longitude']=$match[2];
        $post['latitude']=$match[1];

        
        if ($request->file('personal_image')) {
            $image_name = $request->file('personal_image')->getRealPath();
            Cloudder::upload($image_name, null);
            $imagereturned = Cloudder::getResult();
            $image_id = $imagereturned['public_id'];
            $image_format = $imagereturned['format'];    
            $image_new_name = $image_id.'.'.$image_format;
            $post['personal_image'] = $image_new_name;
        }

        if ($request->file('image_professional_title')) {
            $image_name = $request->file('image_professional_title')->getRealPath();
            Cloudder::upload($image_name, null);
            $imagereturned = Cloudder::getResult();
            $image_id = $imagereturned['public_id'];
            $image_format = $imagereturned['format'];    
            $image_new_name = $image_id.'.'.$image_format;
            $post['image_professional_title'] = $image_new_name;
        }

        if ($request->file('image_profession_license')) {
            $image_name = $request->file('image_profession_license')->getRealPath();
            Cloudder::upload($image_name, null);
            $imagereturned = Cloudder::getResult();
            $image_id = $imagereturned['public_id'];
            $image_format = $imagereturned['format'];    
            $image_new_name = $image_id.'.'.$image_format;
            $post['image_profession_license'] = $image_new_name;
        }

        
        $doctorLawyer = DoctorsLawyers::create($post);

        for($i =0; $i < count($request->service_id); $i ++) {
            DoctorLawyerService::create([
                'service_id' => $request->service_id[$i],
                'doctor_lawyer_id' => $doctorLawyer['id']
            ]);
        }

        if ( $images = $request->file('place_image') ) {
            foreach ($images as $image) {
                $image_name = $image->getRealPath();
                Cloudder::upload($image_name, null);
                $imagereturned = Cloudder::getResult();
                $image_id = $imagereturned['public_id'];
                $image_format = $imagereturned['format'];    
                $image_new_name = $image_id.'.'.$image_format;
                PlaceImage::create(["image" => $image_new_name, "doctor_lawyer_id" => $doctorLawyer['id']]);
            }
        }

        if ($request->sunday_work) {
            
            if (count($request->sunday_from) > 0) {
                for ($i = 0; $i < count($request->sunday_from); $i ++) {
                    $workTime = [];
                    var_dump($request->sunday_from[$i]);
                    var_dump($request->sunday_to[$i]);
                    $sunday['day'] = 0;
                    $sunday['holiday'] = 0;
                    $sunday['from'] = $request->sunday_from[$i];
                    $sunday['to'] = $request->sunday_to[$i];
                    $sunday['doctor_lawyer_id'] = $doctorLawyer['id'];
                    if ($request->reservation_type == "attendance") {
                        $sunday['count'] = $request->sunday_count[$i];
                    }
                    TimesOfWork::create($sunday);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 0;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
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
                    $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
                    if ($request->reservation_type == "attendance") {
                        $workTime['count'] = $request->monday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 1;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
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
                    $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
                    if ($request->reservation_type == "attendance") {
                        $workTime['count'] = $request->tuesday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 2;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
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
                    $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
                    if ($request->reservation_type == "attendance") {
                        $workTime['count'] = $request->wednesday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 3;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
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
                    $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
                    if ($request->reservation_type == "attendance") {
                        $workTime['count'] = $request->thursday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 4;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
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
                    $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
                    if ($request->reservation_type == "attendance") {
                        $workTime['count'] = $request->friday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 5;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
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
                    $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
                    if ($request->reservation_type == "attendance") {
                        $workTime['count'] = $request->saturday_count[$i];
                    }
                    TimesOfWork::create($workTime);
                }
            }
        }else {
            $workTime = [];
            $workTime['day'] = 6;
            $workTime['holiday'] = 1;
            $workTime['doctor_lawyer_id'] = $doctorLawyer['id'];
            TimesOfWork::create($workTime);
        }


        return redirect()->route('doctors&lawyers.index');
    }

    // type : get => edit
    public function EditGet(DoctorsLawyers $drlaw) {
        $data['drlaw'] = $drlaw;
        $data['categories'] = Category::where('deleted', 0)->get();
        $data['drlaw_services'] = $drlaw->drLawServices()->pluck('service_id')->toArray();
        $data['services'] = Service::where('category_id', $data['drlaw']['category_id'])->get();
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

        return view('admin.doctorlawyer_edit', ['data' => $data]);
    }

    // type : post => edit
    public function EditPost(Request $request, DoctorsLawyers $drlaw) {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'app_name_en' => 'required',
            'app_name_ar' => 'required',
            'type' => 'required',
            'category_id' => 'required',
            'email' => 'required',
            'phone' => 'unique:doctors_lawyers,phone,' . $drlaw->id . '|max:255|required',
            'gender' => 'required',
            'professional_title_en' => 'required',
            'professional_title_ar' => 'required',
            'service_id' => 'required',
            'about_en' => 'required',
            'about_ar' => 'required',
            'city_en' => 'required',
            'city_ar' => 'required',
            'address_en' => 'required',
            'address_ar' => 'required',
            'reservation_type' => 'required',
            'recieving_reservation_phone' => 'unique:doctors_lawyers,recieving_reservation_phone,' . $drlaw->id . '|max:255|required',
            'reservation_cost' => 'required'
        ]);

        $post = $request->except(['service_id']);

        if (isset($request->password) && !empty($request->password)) {
            $post['password'] = Hash::make($post['password']);
        }else {
            $post['password'] = $drlaw->password;
        }
        
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
                        $sunday['count'] = $request->sunday_count[$i];
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
                        $workTime['count'] = $request->monday_count[$i];
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
                        $workTime['count'] = $request->tuesday_count[$i];
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
                        $workTime['count'] = $request->wednesday_count[$i];
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
                        $workTime['count'] = $request->thursday_count[$i];
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
                        $workTime['count'] = $request->friday_count[$i];
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
                        $workTime['count'] = $request->saturday_count[$i];
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

        return redirect()->route('doctors&lawyers.index');
        
    }

    // admin approve
    public function adminApprove(DoctorsLawyers $drlaw, $status) {
        $drlaw->update(['active' => $status]);

        return redirect()->back();
    }

    // check phone exist
    public function checkPhoneExist($phone, $type) {
        if ($type == 1) {
            $medLaw = DoctorsLawyers::where('phone', $phone)->select('id')->first();
        }else {
            $medLaw = DoctorsLawyers::where('recieving_reservation_phone', $phone)->select('id')->first();
        }

        $result = 0;
        if (isset($medLaw['id'])) {
            $result = 1;
        }

        return response(json_decode($result), 200);
    }

    // delete place Image
    public function deletePlaceImage(PlaceImage $image) {
        $placeImage = $image->image;
        $publicId = substr($placeImage, 0 ,strrpos($placeImage, "."));    
        Cloudder::delete($publicId);
        $image->delete();

        return redirect()->back();
    }
}