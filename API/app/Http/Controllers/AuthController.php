<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'password'=>'required',
        ]);
        if($validator->fails()){
            return response([
               'status'=>false,
                'errors'=>$validator->errors()
            ],422);
        }
        $credentials=$request->only('email','password');

        if(Auth::attempt($credentials)){
            $token=Auth::user()->createToken('API Token')->plainTextToken;
            return response([
               'status'=>true,
                'token'=>$token,
                'message'=>'login successfully'
            ],200);
        }else{
            return response([
               'status'=>false,
               'message'=>'Invalid email or password'
            ],401);
        }
    }
    public function register(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required',
            'name'=>'required',
        ]);
        if($validator->fails()){
            return response([
                'status'=>false,
                'errors'=>$validator->errors()
            ],422);
        }
        $user=new User();
        $user->email=$request->email;
        $user->name=$request->name;
        $user->password=Hash::make($request->password);
        $user->save();
        //create token for user
        $token=$user->createToken('API Token')->plainTextToken;
        return response([
            'status'=>true,
            'message'=>'Registration successfully created',
            'token'=>$token
            
        ],201);
    }
    public function logout(){
        Auth::user()->tokens()->delete();
        return response([
           'status'=>true,
           'message'=>'logout successfully'
        ],200);
    }
}
