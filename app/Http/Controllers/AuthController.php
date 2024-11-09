<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mockery\Matcher\HasKey;

class AuthController extends Controller
{
    //

    public function register(Request $request){
        $data = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email|email',
            'password'=>'required|confirmed|string|min:8|max:30'
        ]);

        $user = User::create([
            'name'=> $data['name'],
            'email'=>$data['email'],
            'password'=>bcrypt($data['password'])
        ]);
        $token = $user->createToken('token')->plainTextToken;
        $response = [
            'user'=>$user->email,
            'token'=>$token
        ];

        return response($response, 201);

    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return [
            'message'=>'Logged out'
        ];

    }   
    public function login(Request $request){
        $data = $request->validate([
            'email'=>'required|string',
            'password'=>'required|string'
        ]);
        $user = User::where('email', $data['email'])->first(); 

        if(!$user  || !Hash::check($data['password'], $user->password)){
            return response([
                'message'=>'Invalid credentials'
            ], 401);
        }
        $token = $user->createToken('token')->plainTextToken;
        $response = [
            'user'=>$user->email,
            'token'=>$token
        ];

        return response([
            'data'=>$response
        ], 200);
            
    }
}
