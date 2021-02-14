<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Http\Controllers\Controller;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login' , 'register' , 'verifyphone' , 'invalid']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        

        $credentials = request(['phone', 'password']);


        if (! $token = auth()->attempt($credentials)) {
            $response  = APIHelpers::createApiResponse(true , 401 , 'Invalid phone or password' , 'يرجي التاكد من رقم الهاتف او كلمة المرور' , null);
            return response()->json($response, 401);
        }

        if(! $request->fcm_token){
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

        $user = auth()->user();
        $user->verification_code = "1234";
        $user->fcm_token = $request->fcm_token;
        $user->save();
        
		if($user->verified == 1){
			// $responsewithtoken = $this->respondWithToken($token);
			$user->token = 	$this->respondWithToken($token);
		}
       	$response = APIHelpers::createApiResponse(false , 200 , '' , '' , $user);
        return response()->json($response , 200);

    }

    public function invalid(){
        $response = APIHelpers::createApiResponse(true , 401 , 'Invalid Token' , 'تم تسجيل الخروج' , null);
        return response()->json($response , 401);
    }

    /* 
    * create user 
    */
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            "email" => 'required',
            "password" => 'required',
            "fcm_token" => 'required',
            
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

        // check if phone number register before
        $prev_user_phone = User::where('phone', $request->phone)->first();
        if($prev_user_phone){
            $response = APIHelpers::createApiResponse(true , 409 , 'Phone Exists Before' , 'رقم الهاتف موجود من قبل' , null);
            return response()->json($response , 409);
        }

        // check if email registered before
        $prev_user_email = User::where('email', $request->email)->first();
        if($prev_user_email){
            $response = APIHelpers::createApiResponse(true , 409 , 'Email Exists Before' , 'البريد الإلكتروني موجود من قبل' , null);
            return response()->json($response , 409);
        }


        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->fcm_token = $request->fcm_token;
		if($request->gender){
			$user->gender = $request->gender;
		}
        if($request->date_of_birth){
			$user->date_of_birth = $request->date_of_birth;
        }
        $user->verification_code = "1234";
        $user->save();

        // $token = auth()->login($user);
        // $user->token = $this->respondWithToken($token);

        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $user);
        return response()->json($response , 200);
    }


    // verify phone
    public function verifyphone(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            "verification_code" => "required"
        ]);

        if ($validator->fails()) {
            $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
            return response()->json($response , 406);
        }

         $user = User::where('phone' , $request->phone)->first();
         if(!$user){
            $response = APIHelpers::createApiResponse(true , 406 , 'Phone Number Not registered' , 'رقم الهاتف غير مسجل' , null);
            return response()->json($response , 406);
         }

         $verification_code = $user->verification_code;



         if($verification_code == $request->verification_code){
			 $user->verified = 1;
			 $user->save();
            $token = auth()->login($user);
            $user->token = $this->respondWithToken($token);

            $response = APIHelpers::createApiResponse(false , 200 , '' , '' ,$user);
            return response()->json($response , 200);
         }else{
            $response = APIHelpers::createApiResponse(true , 406 , 'Wrong Verification Code' , 'رمز تفعيل غير صحيح' , null);
            return response()->json($response , 406);
         }

        
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth()->user();
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $user);
        return response()->json($response , 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        $response = APIHelpers::createApiResponse(false , 200 , '', '' , []);
        return response()->json($response , 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $responsewithtoken = $this->respondWithToken(auth()->refresh());
        $response = APIHelpers::createApiResponse(false , 200 , '', '' , $responsewithtoken);
        return response()->json($response , 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 432000
        ];
    }
}