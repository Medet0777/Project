<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    //Register user
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ],200);
    }

    //login user
    public function login(Request $request){
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if(!Auth::attempt($data)){
            return response([
               'message' => 'Invalid Credentials.'
            ],403);
        }

        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ],200);
    }

    //logout
    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logged out'
        ],200);
    }

    //get user details
    public function user(){
        return response([
           'user' => auth()->user()
        ],200);
    }

    //update user
    public function update(Request $request)
    {
        $attrs = $request->validate([
           'name' => 'required|string',
        ]);

        $image = $this->saveImage($request->image,'profiles');

        auth()->user()->update([
            'name' => $attrs['name'],
            'image' => $image
        ]);

        return response([
            'message' => 'User updated successfully.',
            'user' => auth()->user()
        ],200);
    }
}
