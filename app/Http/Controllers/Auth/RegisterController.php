<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

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
            'image' => 'file|max:5000',

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
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $prefGender;
        if($data['gender_id']==1){
            $prefGender = 2;
        }else{
            $prefGender = 1;
        }

        $user =  User::create([
            // //User's personal details
            'firstNames' => $data['firstNames'],
            'surname' => $data['surname'],
            'prefName'=> $data['prefName'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phoneNumber' => $data['phoneNumber'],
            'city_id' => (int) $data['city_id'],
            'gender_id' => (int) $data['gender_id'],
            'marital_status_id' => (int) $data['marital_status_id'], 
            'dob' => $data['dob'],
            'age' => Carbon::parse($data['dob'])->diff(Carbon::now())->format('%y'),
            'numOfChildren' => $data['numOfChildren'],
            'bio' => $data['bio'],
            //imageAddress NOT COMPLETE


            //User's partner preferences
            'prefMinAge' => (int) $data['prefMinAge'],
            'prefMaxAge' => (int) $data['prefMaxAge'],
            'prefMaxNumOfChildren' => (int) $data['prefMaxNumOfChildren'],
        ]);

        $user->prefCities()->sync((int) $data['prefCities']);
        $user->prefGenders()->sync((int) $prefGender);
        $user->prefMaritalStatuses()->sync((int) $data['prefMaritalStatuses']);

        return $user;
    }
}
