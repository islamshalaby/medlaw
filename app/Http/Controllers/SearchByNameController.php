<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DoctorsLawyers;
use App\Helpers\APIHelpers;

class SearchByNameController extends Controller
{
        public function Search(Request $request)
        {
            $search = $request->query('search');

            if(! $search){
                $response = APIHelpers::createApiResponse(true , 406 , 'Missing Required Fields' , 'بعض الحقول مفقودة' , null);
                return response()->json($response , 406);
            }

            $type = $request->type;
            $doctors_lawyers = DoctorsLawyers::select('app_name_ar' , 'app_name_en' , 'id')->Where('type' , $type)->Where(function($query) use ($search) {
                $query->Where('app_name_ar', 'like', '%' . $search . '%')->orWhere('app_name_en', 'like', '%' . $search . '%');
            })->get();    
            $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $doctors_lawyers);
            return response()->json($response , 200);
        }
}
