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
        $regex = "/^([^0-9?;@#~{}><:!£$%^&*()¬`|=_+]*]*)$/";
        return Validator::make($request, [
      //Validate user's personal details
      'firstNames' => ['required', 'string', 'regex:'.$regex],
      'surname' => ['required', 'string', 'regex:'.$regex],
      'prefName'=>['required', 'string', 'regex:'.$regex],
      'email' => 'required|email|unique:users',
<<<<<<< HEAD
      'password' => 'required|confirmed|min:8',
      'phoneNumber' => 'required|string|max:11|regex:/(0)[0-9]{10}/|unique:users',
=======
      'phoneNumber' => 'required|string|confirmed|max:11|regex:/(0)[0-9]{10}/|unique:users',
>>>>>>> 2518ab794cb21c0c23f6a26678255d6f921e343e
      'city_id' => 'required|integer|min:1|max:3',
      'gender_id'=>'required|integer|min:1|max:2',
      'marital_status_id'=>'required|integer|min:1|max:3',
      'dob'=>'required|date|before:18 years ago|after:70 years ago',
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

            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                ]);
            }

            $user =  User::create([
                // //User's personal details
                'firstNames' => trim($request['firstNames']),
                'surname' => trim($request['surname']),
                'prefName'=> trim($request['prefName']),
                'email' => trim($request['email']),
                'password' => bcrypt($request['password']),
                'phoneNumber' => trim($request['phoneNumber']),
                'dob' => Carbon::createFromFormat('Y/m/d', $request['dob']),
                'numOfChildren' => (int) $request['numOfChildren'],
                'bio' => trim($request['bio']),
                'city_id' => (int) $request['city_id'],
                'gender_id' => (int) $request['gender_id'],
                'marital_status_id' => (int) $request['marital_status_id'],
                // TODO: imageAddress

                //User's partner preferences
                'prefMinAge' => (int) $request['pref_min_age'],
                'prefMaxAge' => (int) $request['pref_max_age'],
                'prefMaxNumOfChildren' => (int) $request['pref_num_of_children'],
            ]);

            //User's partner preferences
            $user->prefCities()->sync((int) $request['pref_cities']);
            $user->prefGenders()->sync((int) $request['pref_genders']);
            $user->prefMaritalStatuses()->sync((int) $request['pref_marital_statuses']);

            $user->sendEmailVerificationNotification();

            $user = User::where('id', $user->id)
                        ->first()
                        ->makeVisible([
                        'firstNames',
                        'surname',
                        'email',
                        'phoneNumber',
                        'dob',
                        ]);

            $token['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'message' => "Verification Email Sent to " . $user->email,
                'token' => $token,
                'user' => $user,
            ], 201);
        }
}