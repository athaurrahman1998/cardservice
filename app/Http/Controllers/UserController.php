<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //Register Function 

    public function register(Request $request) {
        
        $requestObject  = $request->all();
        $rules = [
            'first_name' => 'required',            
            'email' => 'bail|required|unique:App\User|email:rfc,dns',
            'password' => 'bail|required|min:8|max:20|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
            'contact_no' => 'required',
        ];

        $validator = Validator::make($requestObject,$rules);
        if($validator ->fails()){
            return response()->json(['status' => false,'message' => $validator->errors()],400);
        } else {
            $firstname = $request->first_name;
            $lastname = $request->last_name;

            $store = new User();
            $store->first_name = $firstname;
            $store->last_name = $lastname;
            $store->email = $request->email;
            $store->password = Hash::make($request->password);
            $store->contact_no = $request->contact_no;
            $store->username = $firstname.$lastname;
            $store->save();

            if($store==true){
                $getid = $store->id;
                $get_details = User::where('id',$getid)->select()->first();
                return response()->json(['status' => true,'data' => $get_details],200);
            } else {
                return response()->json(['status'=>false,'message' => 'Couldnt create the user'], 400);
            }            
        }
    }

    public function login(Request $request){
        $loginObject = $request->all();
        $loginrules = [
            'email_name' => 'required|max:255',
            'password' => 'required'
        ];

        $loginValidator = Validator::make($loginObject,$loginrules);
        if($loginValidator->fails()){
            return response()->json(['status'=>false,'message'=>$loginValidator->errors()],400);
        }

        $loginuser_details = User::where('email',$request->email_name)->orWhere('username',$request->email_name)->select()->first();
        if($loginuser_details) {
            $verified = Hash::check($request->password, $loginuser_details->password);
            if ($verified) {
                return response()->json(['status'=>true,'data'=>$loginuser_details,'message'=>'User Returned'], 200);
            } else {
                return response()->json(['status'=>false,'message' => 'Sorry The password is incorrect'], 400);
            }
        } else {
            return response()->json(['status'=>false,'message' => 'Invalid Credentials'], 400);
        }
    }

}
