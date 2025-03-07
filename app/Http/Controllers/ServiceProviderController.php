<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service_Provider;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ImageController;

class ServiceProviderController extends Controller
{
    public function showAll() {
        $service = Service_Provider::all();

        $res["status"] = true;
        $res["message"] = "All service provider";
        $res["data"] = $service;
        return response()->json($res, 200);
    }

    public function show($id)
    {
        $service = Service_Provider::find($id);
        if($service){
            $res["status"] = fasle;
            $res["message"] = "One service provider";
            $res["data"] = $service;
            return response()->json($res, 200);
        }else{

            $res["status"] = fasle;
            $res["message"] = "Not found";
            return response()->json($res, 404);
        }
    }

    public function byCategory($category_id) {
        $res = array();
        
        try {
            $services = Service_Provider::where('category_id', $category_id);
            
            
            if(!$services->isEmpty()) {
                $res['status'] = 200;
                $res['message'] = "Retrieved service providers";
                $res['data'] = $services;
                
            } else {    
                $res['status'] = 404;
                $res['message'] = "No service providers in this category";
            }
        } catch(Exception $e) {
            $res['status'] = 501;
            $res['message'] = "An error occurred trying to retrieve service providers";
        }

        return response()->json($res, $res['status']);
    }

    public function create(Request $request)
    {
         $validator = Validator::make($request->all(), [
               'name' => 'required|string|min:3',
               'phone' => 'required',
               'description' => 'required',
               'image' => 'required',
               'estate_id' => 'required|int'
          ]);

        if ($validator->fails()) {
        return ['message' => 'Please fill all Fields']; 
        }
        //start temporay transaction
        DB::beginTransaction();
        try{

            $service = Service_Provider::create($request->all());

            //if operation was successful save commit save to database
            DB::commit();
            $res["status"] = true;
            $res["message"] = "Service Provider created";
            $res["data"] = $service;
            return response()->json($res, 200);

        }catch(\Exception $e) {
            //rollback what is saved
            DB::rollBack();

            $res['status'] = false;
            $res['message'] = 'An error occured, please try again!';
            $res['hint'] = $e->getMessage();
            return response()->json($res, 501);

        }

    }

     public function update(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
           'name'        => 'required|string|min:3',
           'phone'       => 'required',
           'description' => 'required',
           'image'       => 'required',
           'estate_id'   => 'required|int'
      ]);

        if ($validator->fails()) {
        return ['message' => 'Please fill all Fields']; 
        }
        //start temporay transaction
        DB::beginTransaction();
        try{    
            $service              = Service_Provider::find($id);
            $service->name        = $request->input("name");
            $service->phone       = $request->input("phone");
            $service->description = $request->input("description");
            $service->image       = $request->input("image");
            $service->estate_id   = $request->input("estate_id");
            $service->save();

             //if operation was successful save commit save to database
            DB::commit();
            $res["status"]  = true;
            $res["message"] = "Service provider Updated Successfully!";
            return response()->json($res, 200);
        }catch(\Exception $e) {
            //rollback what is saved
            DB::rollBack();

            $res['status'] = false;
            $res['message'] = 'An error occured, please try again!';
            $res['hint'] = $e->getMessage();
            return response()->json($res, 501);

        }
    }


    public function destroy($id)
    {
        $service = Service_Provider::destroy($id);

        if($service){
            $res['status'] = true;
            $res["message"] = $service." Service Provider Deleted!";
            return response()->json($res, 200);
        }else{
            $res['status'] = false;
            $res["message"] = "An error occured, please try again";
            return response()->json($res, 501);
        }
    }

    public function upload(Request $request, ImageController $image) {
        $this->validate($request, [
         'image' => "image|max:4000|required",
        ]);
        
        $res = $image->imageUpload($request);
        return response()->json($res, $res['status_code']);
    }

}
