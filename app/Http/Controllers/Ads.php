<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\APIHelpers;
use App\Ad;

class Ads extends Controller
{
    /**
     * Display a listing of main ads.
     */
    public function GetMainAds($lang , $v)
    {
        $mainAds = Ad::where('place' , 1)->get();
        $response = APIHelpers::createApiResponse(false , 200 , '' ,'' , $mainAds);
        return response()->json($response , 200);
    }

    /**
     * Display a listing of ads in filter screen.
     */
    public function GetFilterScreenAds($type , $lang , $v)
    {
        $ads = Ad::where('place' , 2)->where('adownertype' , $type)->get();
        $response = APIHelpers::createApiResponse(false , 200 , '' , '' , $ads);
        return response()->json($response , 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
