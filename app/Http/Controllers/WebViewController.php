<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;

class WebViewController extends Controller
{
    // get about
    public function getabout(Request $request){
        $data['lang'] = $request->lang;
        $setting = Setting::find(1);
        if($data['lang'] == 'en' ){
            $data['text'] = $setting['termsandconditions_en'];
        }else{
            $data['text'] = $setting['termsandconditions_ar'];
        }
        return view('webview.about' , $data);
    }

    // get terms and conditions
    public function gettermsandconditions(Request $request){
        $data['lang'] = $request->lang;
        $setting = Setting::find(1);
        if($data['lang'] == 'en' ){
            $data['title'] = 'Terms and Conditions';
            $data['text'] = $setting['aboutapp_en'];
        }else{
            $data['title'] = 'الشروط و الأحكام';
            $data['text'] = $setting['aboutapp_ar'];
        }
        return view('webview.termsandconditions' , $data);
    }
}
