<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;


class UsersController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'firstNames' => 'required|string',
                'surname' => 'required|string',
                'prefName'=>'required|string',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|confirmed',
                'phoneNumber' => 'required|unique:users|regex:/(0)[0-9]{10}/',
                'city' => 'required|string',
                'maritalStatus'=>'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                ], 401);
            }

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);

            $success['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $success,
                'user' => $user
            ]);
        }
  
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
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('appToken')->accessToken;
           //if authentication successfull
            return response()->json([
              'success' => true,
              'token' => $success,
              'user' => $user
          ]);
        } else {
       //if authentication is unsuccessfull
          return response()->json([
            'success' => false,
            'message' => 'Invalid Email or Password',
        ], 401);
        }
    }

  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout()
    {
      //if user is logged in
      if (Auth::user()) {
        $user = Auth::user()->token();
        $user->revoke();
      //if logout request worked
        return response()->json([
          'success' => true,
          'message' => 'Logout success'
      ]);
      }
      else {
      //if lougout request failed
        return response()->json([
          'success' => false,
          'message' => 'Unable to Logout'
        ]);
      }
    }


    /**
     * Show a list of all of the application's users.
     *
     * @return Response
     */
    public function allUsers()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return $users;
    }

  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}