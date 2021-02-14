<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\DB;
use App\Service;
use App\Category;

class ServiceController extends AdminController{
    // index
    public function show() {
        $data['services'] = Service::get();

        return view('admin.services', ['data' => $data]);
    }

    // type : get => add
    public function AddGet() {
        $data['categories'] = Category::where('deleted', 0)->get();

        return view('admin.service_form', ['data' => $data]);
    }

    // type : post => add
    public function AddPost(Request $request) {
        $post = $request->all();

        Service::create($post);

        return redirect()->route('services.index');
    }

    // type : get => edit
    public function EditGet(Service $service) {
        $data['service'] = $service;
        $data['categories'] = Category::where('deleted', 0)->get();

        return view('admin.service_edit', ['data' => $data]);
    }

    // type : post => edit
    public function EditPost(Request $request, Service $service) {
        $post = $request->all();

        $service->update($post);

        return redirect()->route('services.index');
    }

    // delete
    public function delete(Service $service) {
        $service->delete();

        return redirect()->back();
    }

    // type : get => fetchCategoriesByType
    public function fetchCategoriesByType($type) {
        $rows = Category::where('type', $type)->where('deleted', 0)->get();
        $data = json_decode(($rows));


        return response($data, 200);
    }
}