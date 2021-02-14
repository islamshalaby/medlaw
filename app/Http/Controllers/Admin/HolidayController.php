<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Holiday;
use App\DoctorsLawyers;

class HolidayController extends AdminController{
    // index
    public function show() {
        $data['holidays'] = Holiday::get();
        $data['drLawyers'] = DoctorsLawyers::where('active', 1)->get();

        return view('admin.holidays', ['data' => $data]);
    }

    // type : post set holiday
    public function postSetHoliday(Request $request) {
        $post = $request->all();
        
        Holiday::create($post);

        return redirect()->back();
    }

}