<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Setting;

class SettingController extends Controller
{
    public function GetSettings(){
        $settings = Setting::find(1);
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $settings);
        return response()->json($response , 200);
    }

}
