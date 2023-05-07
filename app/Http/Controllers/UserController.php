<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'phone'=>'required',
            'password'=>'required|min:8',
        ]);

        if($validator->fails())
        {
            return response()->json(['status'=>'fail','validation_error'=>$validator->errors()]);
        }

        $data= $request->all();
        $data['password']=Hash::make($request->password);

        $user = User::create($data);

        if($user)
        {
            return response()->json(['status'=>'Success','Message'=>'User Reg Successfully',
                'data' => $user]);
        }

        return response()->json(['status'=>'Fail','Message'=>'User Reg Fail']);
    }

    public function login(Request $request) {

        $validator = Validator::make($request->all(),[
            'email'=>'required',
            'password'=>'required|min:8',
        ]);

        if($validator->fails())
        {
            return response()->json(['status'=>'fail','validation_error'=>$validator->errors()]);
        }

        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password]))
        {
            $user = Auth::user();
            $token = $user->createToken('usertoken')->accessToken;
            return response()->json(['status'=>'success','login'=>true, 'token'=>$token]);
        }
        else
        {
            return response()->json(['status'=>'fail','Message'=>'Whoops! email or password invalid']);
        }
    }

    public function userDetail()
    {
        $user = Auth::user();
        if($user)
        {
            return response()->json(['status'=>'Success','user_data' => $user]);
        }
        return response()->json(['status'=>'Fail','Message'=>'User Not Found']);
    }
}
