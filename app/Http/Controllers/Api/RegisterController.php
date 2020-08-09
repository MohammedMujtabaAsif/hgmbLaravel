<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\User;

class RegisterController extends Controller
{

    use RegistersUsers;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Array $request)
    {
        return Validator::make($request, [
            //Validate user's personal details
            'firstNames' => 'required|string',
            'surname' => 'required|string',
            'prefName'=>'required|string',
            'email' => 'required|unique:users|email',
            'password' => 'required|string|confirmed',
            'phoneNumber' => 'required|string|max:11|regex:/(0)[0-9]{10}/|unique:users',
            'dob'=>'required|date|before:18 years ago|after:70 years ago',
            'numOfChildren'=>'integer',
            'bio'=>'required|string|max:1000',
            'city_id' => 'required|integer|min:1|max:3',
            'gender_id'=>'required|integer|min:1|max:2',
            'marital_status_id'=>'required|integer|min:1|max:3',
            'prefMinAge'=>'required|integer|min:18|lt:prefMaxAge',
            'prefMaxAge'=>'required|integer|min:20|gt:prefMinAge',
            'prefMaxNumOfChildren'=>'required|integer',
            'image' => 'file|max:5000',

            //Validate user's partner preferences
            'pref_cities' => 'required|array|distinct',
            'pref_cities.*' => 'integer|min:1|max:3',
            'pref_genders'=>'required|array|distinct',
            'pref_genders.*'=>'integer|min:1|max:2',
            'pref_marital_statuses'=>'required|array|distinct',
            'pref_marital_statuses.*'=>'integer|min:1|max:3',
        ]);
    }


    /**
     * Create user
     *
     * @param  Request $request User details
     *
     * @return Response success User
     */
    public function register(Request $request)
        {          

            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                ]);
            }

            $user =  User::create([
                // //User's personal details
                'firstNames' => $request['firstNames'],
                'surname' => $request['surname'],
                'prefName'=> $request['prefName'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'phoneNumber' => $request['phoneNumber'],
                'dob' => Carbon::createFromFormat('dd/mm/yyyy', $request['dob']),
                'numOfChildren' => (int) $request['numOfChildren'],
                'bio' => $request['bio'],
                'city_id' => (int) $request['city_id'],
                'gender_id' => (int) $request['gender_id'],
                'marital_status_id' => (int) $request['marital_status_id'],
                // TODO: imageAddress

                //User's partner preferences
                'prefMinAge' => (int) $request['prefMinAge'],
                'prefMaxAge' => (int) $request['prefMaxAge'],
                'prefMaxNumOfChildren' => (int) $request['prefMaxNumOfChildren'],
            ]);

            $user->prefCities()->sync((int) $request['pref_cities']);
            $user->prefGenders()->sync((int) $request['pref_genders']);
            $user->prefMaritalStatuses()->sync((int) $request['pref_marital_statuses']);

            $token['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
            ], 201);
        }
}