<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Holiday;
use App\User;

class HolidayController extends DashboardController{
    // index
    public function show() {
        $data['holidays'] = Holiday::where('doctor_lawyer_id', auth()->user()->id)->get();

        return view('dashboard.holidays', ['data' => $data]);
    }

    // type : post set holiday
    public function postSetHoliday(Request $request) {
        $post = $request->all();
        $post['doctor_lawyer_id'] = auth()->user()->id;
        Holiday::create($post);

        return redirect()->back();
    }

}