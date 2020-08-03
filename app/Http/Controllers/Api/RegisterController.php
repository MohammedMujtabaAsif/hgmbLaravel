<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RegisterController extends Controller
{

    use RegistersUsers;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            //Validate user's personal details
            'firstNames' => 'required|string',
            'surname' => 'required|string',
            'prefName'=>'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'phoneNumber' => 'required|unique:users|max:11|regex:/(0)[0-9]{10}/|string',
            'city_id' => 'required|integer',
            'gender_id'=>'required|integer',
            'marital_status_id'=>'required|integer',
            'dob'=>'required|date|before:18 years ago',
            'numOfChildren'=>'integer',
            'bio'=>'required|string|max:1000',

            //Validate user's partner preferences
            'prefCities' => 'required|integer|array|distinct',
            'prefGenders'=>'required|integer|array|distinct',
            'prefMaritalStatuses'=>'required|integer|array|distinct',
            'prefMinAge'=>'required|integer|min:18|lt:prefMaxAge',
            'prefMaxAge'=>'required|integer|min:20|gt:prefMinAge',
            'prefMaxNumOfChildren'=>'required|integer',
        ]);
    }


/**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register(Request $request)
        {
            $validator = Validator::make($request->all(), [
            //Validate user's personal details
            'firstNames' => 'required|string',
            'surname' => 'required|string',
            'prefName'=>'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'phoneNumber' => 'required|unique:users|max:11|regex:/(0)[0-9]{10}/|string',
            'city_id' => 'required|integer',
            'gender_id'=>'required|integer',
            'marital_status_id'=>'required|integer',
            'dob'=>'required|date|before:18 years ago',
            'numOfChildren'=>'integer',
            'bio'=>'required|string|max:1000',
            'image' => 'file|max:5000',

            //Validate user's partner preferences
            'prefCities' => 'required|integer|array|distinct',
            'prefGenders'=>'required|integer|array|distinct',
            'prefMaritalStatuses'=>'required|integer|array|distinct',
            'prefMinAge'=>'required|integer|min:18|lt:prefMaxAge',
            'prefMaxAge'=>'required|integer|min:20|gt:prefMinAge',
            'prefMaxNumOfChildren'=>'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'code' => 400,
                ]);
            }

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);            
            $input['age'] = Carbon::parse($input['dob'])->diff(Carbon::now())->format('%y');
            $user = User::create($input);

            $token['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
                'code' => 201,
            ]);
        }
}