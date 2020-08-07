<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;

class LoginController extends Controller
{
    /**
        * Login user and create token
        *
        * @param  [string] email
        * @param  [string] password
        * @param  [boolean] remember_me
        * @return [string] access_token
        * @return [string] token_type
        * @return [string] expires_at
        */
    public function login()
    {
        //Validate The request data      
        $validator = Validator::make(request()->all(),[
            'email' => 'required|email',
            'password' => 'required'
            ]);

        if ($validator->fails()) {
            return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            ], 400);
        }

        //Attempt to login user with request details
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            //if successful, get User and Create token
            $user = Auth::user();
            $token['token'] = $user->createToken('appToken')->accessToken;

            // return the User and Token with positive success as JSON
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
            ], 200);
        }

        
        // return an unsuccessful JSON error message
        return response()->json([
            'success' => false,
            'message' => 'Invalid Email or Password',
        ], 401);
        
    }



    /**
        * Logout user (Revoke the token)
        *
        * @return [string] message
        */
    public function logout(Request $request)
    {
        // Attempt to revoke user's token
        if ($request->user()->token()->revoke()) {
            //if logout request worked return positive message with success code (200)
            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ]);
        }
        
        //if lougout request failed return negative message
        return response()->json([
            'success' => false,
            'message' => 'Unable to Logout',
        ]);
    }
}
