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

            $prefGender;

            if($data['gender_id']==1){
                $prefGender = 2;
            }else{
                $prefGender = 1;
            }

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

            $user =  User::create([
                // //User's personal details
                'firstNames' => $validator['firstNames'],
                'surname' => $validator['surname'],
                'prefName'=> $validator['prefName'],
                'email' => $validator['email'],
                'password' => bcrypt($validator['password']),
                'phoneNumber' => $validator['phoneNumber'],
                'city_id' => (int) $validator['city_id'],
                'gender_id' => (int) $validator['gender_id'],
                'marital_status_id' => (int) $validator['marital_status_id'], 
                'dob' => Carbon::createFromFormat('Y-m-d', input['dob']),
                'age' => Carbon::parse($validator['dob'])->diff(Carbon::now())->format('%y'),
                'numOfChildren' => $validator['numOfChildren'],
                'bio' => $validator['bio'],
                // TODO: imageAddress


                //User's partner preferences
                'prefMinAge' => (int) $validator['prefMinAge'],
                'prefMaxAge' => (int) $validator['prefMaxAge'],
                'prefMaxNumOfChildren' => (int) $validator['prefMaxNumOfChildren'],
            ]);

            $user->prefCities()->sync((int) $validator['prefCities']);
            $user->prefGenders()->sync((int) $prefGender);
            $user->prefMaritalStatuses()->sync((int) $validator['prefMaritalStatuses']);

        //     $input = $request->all();
        //     $input['password'] = Hash::make($input['password']);
        //     $input['dob'] = Carbon::createFromFormat('Y-m-d', input['dob']);
        //     $input['age'] = Carbon::parse($input['dob'])->diff(Carbon::now())->format('%y');
        //     $user = User::create($input);


        // $user->prefCities()->sync((int) $data['prefCities']);
        // $user->prefGenders()->sync((int) $prefGender);
        // $user->prefMaritalStatuses()->sync((int) $data['prefMaritalStatuses']);

            $token['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
                'code' => 201,
            ]);
        }
}