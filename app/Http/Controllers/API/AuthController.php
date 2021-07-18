<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
     {
        $validated = $request->validate([
            'name' => 'required|max:55',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'isManager' => 'required'
        ]);


        $validated['password'] = bcrypt($request->password);

        $user = User::create($validated);

        $accessToken = $user->createToken('authToken')->accessToken;
        
        return response(['user' => $user, 'access_token' => $accessToken]);

    }

    public function login(Request $request) 
    {
        $login = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if(! Auth::attempt($login))
             return response(['message' => 'Invalid Credentials'], 401);

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }
}
 