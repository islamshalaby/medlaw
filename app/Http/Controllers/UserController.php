<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\APIHelpers;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api' , ['except' => ['resetforgettenpassword' , 'checkphoneexistance']]);
    }

    public function getprofile(Request $request){
        $user = auth()->user();
        $returned_user['user_name'] = $user['name'];
        $returned_user['phone'] = $user['phone'];
        $returned_user['wallet'] = $user['wallet'];
        $returned_user['email'] = $user['email'];
        $returned_user['gender'] = $user['gender'];
        $returned_user['dob'] = $user['date_of_birth'];		
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $returned_user);
        return response()->json($response , 200);  
    }

    public function updateprofile(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            "email" => 'required',
            "date_of_birth" => 'required',
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

        $currentuser = auth()->user();
        $user_by_phone = User::where('phone' , '!=' , $currentuser->phone )->where('phone', $request->phone)->first();
        if($user_by_phone){
            $response = APIHelpers::createApiResponse(true , 409 , 'Phone Exists Before' , 'رقم الهاتف موجود من قبل' , null);
            return response()->json($response , 409);
        }

        $user_by_email = User::where('email' , '!=' ,$currentuser->email)->where('email' , $request->email)->first();
        if($user_by_email){
            $response = APIHelpers::createApiResponse(true , 409 , 'Email Exists Before' , 'البريد الإلكتروني موجود من قبل' , null);
            return response()->json($response , 409); 
        }

        User::where('id' , $currentuser->id)->update([
            'name' => $request->name , 
            'phone' => $request->phone , 
            'email' => $request->email , 
            'date_of_birth' => $request->date_of_birth 
             ]);

        $newuser = User::find($currentuser->id);
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $newuser);
        return response()->json($response , 200);    
    }


    public function resetpassword(Request $request){
        $validator = Validator::make($request->all() , [
            'password' => 'required',
			"old_password" => 'required'
        ]);

        if($validator->fails()) {
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

		$user = auth()->user();
		if(!Hash::check($request->old_password, $user->password)){
			$response = APIHelpers::createApiResponse(true , 406 , 'Wrong old password' , 'كلمه المرور السابقه خطأ' , null , $request->lang);
            return response()->json($response , 406);
		}
		if($request->old_password == $request->password){
			$response = APIHelpers::createApiResponse(true , 406 , 'You cannot set the same previous password' , 'لا يمكنك تعيين نفس كلمه المرور السابقه' , null , $request->lang);
            return response()->json($response , 406);
		}
        User::where('id' , $user->id)->update(['password' => Hash::make($request->password)]);
        $newuser = User::find($user->id);
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $newuser);
        return response()->json($response , 200);
    }

    public function resetforgettenpassword(Request $request){
        $validator = Validator::make($request->all() , [
            'password' => 'required',
            'phone' => 'required'
        ]);

        if($validator->fails()) {
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

        $user = User::where('phone', $request->phone)->first();
        if(! $user){
            $response = APIHelpers::createApiResponse(true , 403 , 'Phone Not Exists Before' , 'رقم الهاتف غير موجود' , null);
            return response()->json($response , 403);
        }

        User::where('phone' , $user->phone)->update(['password' => Hash::make($request->password)]);
        $newuser = User::where('phone' , $user->phone)->first();
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $newuser);
        return response()->json($response , 200);
    }

    // check if phone exists before or not
    public function checkphoneexistance(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'phone' => 'required'
        ]);

        if($validator->fails()) {
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'حقل الهاتف اجباري' , null);
            return response()->json($response , 406);
        }
        
        $user = User::where('phone' , $request->phone)->first();
        if($user){
			$user->verification_code = "1235";
        	$user->save();
            $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $user);
            return response()->json($response , 200);
        }

        $response = APIHelpers::createApiResponse(true , 403 , 'Phone Not Exists Before' , 'الهاتف غير موجود من قبل' , null);
        return response()->json($response , 403);

    }

    // Get Wallet Ballance
    public function getwalletbalance(){
        $user = auth()->user();
        $newuser['wallet'] = $user->wallet;
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $newuser);
        return response()->json($response , 200);

    }

    // Update Wallet Balance
    public function updatewalletbalance(Request $request){
         $validator = Validator::make($request->all() , [
            'amount' => 'required'
         ]);
         if($validator->fails()){
             $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
             return response()->json($response , 406);
         }
         
         $user = auth()->user();
         $currentWalletAmount = $user->wallet;
         $newWalletAmount = $currentWalletAmount + $request->amount;
         User::where('id' , $user->id)->update(['wallet' => $newWalletAmount]);
         $user = User::find($user->id);
         $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $user);
         return response()->json($response , 200);
    }

}
