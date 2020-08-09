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
            'city_id' => 'required|integer|min:1|max:3',
            'gender_id'=>'required|integer|min:1|max:2',
            'marital_status_id'=>'required|integer|min:1|max:3',
            'dob'=>'required|date|before:18 years ago|after:65 years ago',
            'numOfChildren'=>'integer',
            'bio'=>'required|string|max:1000',
            'image' => 'file|max:5000',

            //Validate user's partner preferences
            'pref_cities' => 'required|array|distinct',
            'pref_cities.*' => 'integer|min:1|max:3',
            'pref_genders'=>'required|array|distinct',
            'pref_genders.*'=>'integer|min:1|max:2',
            'pref_marital_statuses'=>'required|array|distinct',
            'pref_marital_statuses.*'=>'integer|min:1|max:3',
            'pref_min_age'=>'required|integer|min:18',
            'pref_max_age'=>'required|integer|min:20|gt:pref_min_age',
            'pref_num_of_children'=>'required|integer',
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

            $prefGender =1;

            if($request['gender_id']==1){
                $prefGender = 2;
            }            

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

            $user->prefCities()->sync((int) $request['prefCities']);
            $user->prefGenders()->sync((int) $request['prefGenders']);
            $user->prefMaritalStatuses()->sync((int) $request['prefMaritalStatuses']);

            $token['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
            ], 201);
        }
}