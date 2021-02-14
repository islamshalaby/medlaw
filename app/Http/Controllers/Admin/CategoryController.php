<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\DB;
use App\Category;

class CategoryController extends AdminController{

    // type : get -> to add new
    public function AddGet(){
        return view('admin.category_form');
    }

    // type : post -> add new category
    public function AddPost(Request $request){
        $image_name = $request->file('image')->getRealPath();
        Cloudder::upload($image_name, null);
        $imagereturned = Cloudder::getResult();
        $image_id = $imagereturned['public_id'];
        $image_format = $imagereturned['format'];    
        $image_new_name = $image_id.'.'.$image_format;

        $vector_name = $request->file('vector')->getRealPath();
        Cloudder::upload($vector_name, null);
        $vectorreturned = Cloudder::getResult();
        $vector_id = $vectorreturned['public_id'];
        $vector_format = $vectorreturned['format'];    
        $vector_new_name = $vector_id.'.'.$vector_format;


        $category = new Category();
        $category->image = $image_new_name;
        $category->vector = $vector_new_name;
        $category->title_en = $request->title_en;
        $category->title_ar = $request->title_ar;
        $category->type = $request->type;
        $category->save();
        return redirect('admin-panel/categories/show'); 
    }

    // get all categories
    public function show(){
        $data['categories'] = Category::where('deleted' , 0)->orderBy('id' , 'desc')->get();
        return view('admin.categories' , ['data' => $data]);
    }

    // get edit page
    public function EditGet(Request $request){
        $data['category'] = Category::find($request->id);
        return view('admin.category_edit' , ['data' => $data ]);
    }

    // edit category
    public function EditPost(Request $request){
        $category = Category::find($request->id);
        if($request->file('image')){
            $image = $category->image;
            $publicId = substr($image, 0 ,strrpos($image, "."));    
            Cloudder::delete($publicId);
            $image_name = $request->file('image')->getRealPath();
            Cloudder::upload($image_name, null);
            $imagereturned = Cloudder::getResult();
            $image_id = $imagereturned['public_id'];
            $image_format = $imagereturned['format'];    
            $image_new_name = $image_id.'.'.$image_format;
            $category->image = $image_new_name;
        }

        if($request->file('vector')){
            $vector = $category->vector;
            $publicId = substr($vector, 0 ,strrpos($vector, "."));    
            Cloudder::delete($publicId);
            $vector_name = $request->file('vector')->getRealPath();
            Cloudder::upload($vector_name, null);
            $vectorreturned = Cloudder::getResult();
            $vector_id = $vectorreturned['public_id'];
            $vector_format = $vectorreturned['format'];    
            $vector_new_name = $vector_id.'.'.$vector_format;
            $category->vector = $vector_new_name;
        }


        $category->title_en = $request->title_en;
        $category->title_ar = $request->title_ar;
        $category->type = $request->type;
        $category->save();
        return redirect('admin-panel/categories/show');
    }

    // delete category
    public function delete(Request $request){
        $category = Category::find($request->id);
        $category->deleted = 1;
        $category->save();
        return redirect()->back();
    }

}