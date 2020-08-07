<?php

namespace App\Http\Controllers\Auth;

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
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $prefGender = 1;
        
        if($data['gender_id']==1)
            $prefGender = 2;

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
