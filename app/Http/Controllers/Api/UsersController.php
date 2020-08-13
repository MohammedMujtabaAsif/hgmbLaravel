<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Demency\Friendships\Traits\Friendable;
use Illuminate\Pagination\Factory;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User;

class UsersController extends Controller
{

  /**
    * Get the authenticated User
    *
    * @return Response success User
    */
  public function index()
  {    
    $user = User::where('id', auth()->user()->id)
                ->first()
                ->makeVisible([
                  'firstNames',
                  'surname',
                  'email',
                  'phoneNumber',
                  'dob',
                ]);

    if(is_null($user))
      return response()->json([
        'success' => false,
        'message' => 'User Not Found',
      ]);
    
    return response()->json([
      'success' => true,
      'data' => $user
    ]);
  }


  /**
    * Validate then Update the authenticated user's details to request details
    *
    * @param Request $request user's account details (except password)
    *
    * @return Response success User
    */
  public function update(Request $request){
    $user = $request->user();


    $validator = Validator::make($request->all(), [
      //Validate user's personal details
      'firstNames' => 'required|string',
      'surname' => 'required|string',
      'prefName'=>'required|string',
      'email' => 'required|unique:users,email,' . $request->user()->id,
      'phoneNumber' => 'required|string|max:11|regex:/(0)[0-9]{10}/|unique:users,phoneNumber,' . $request->user()->id,
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

    if($validator->fails()){
      return response()->json([
        'success' => false,
        'message' => $validator->errors(),
      ]);
    }

    $user->update([
      // //User's personal details
      'firstNames' => $request['firstNames'],
      'surname' => $request['surname'],
      'prefName'=> $request['prefName'],
      'email' => $request['email'],
      'phoneNumber' => $request['phoneNumber'],
      'city_id' => (int) $request['city_id'],
      'gender_id' => (int) $request['gender_id'],
      'marital_status_id' => (int) $request['marital_status_id'],
      'dob' => Carbon::createFromFormat('Y/m/d', $request['dob']),
      'numOfChildren' => $request['numOfChildren'],
      'bio' => $request['bio'],
      // TODO: imageAddress


      //User's partner preferences
      'prefMinAge' => $request['pref_min_age'],
      'prefMaxAge' => $request['pref_max_age'],
      'prefMaxNumOfChildren' => $request['pref_num_of_children'],
      
    ]);

    $user->prefCities()->sync($request['pref_cities']);
    $user->prefGenders()->sync($request['pref_genders']);
    $user->prefMaritalStatuses()->sync($request['pref_marital_statuses']);

    $user->adminApproved = 0;

    $user->save();

    return $this->index();
  }


  /**
    * Check user knows account password
    * Delete the authenticated user
    *
    * @param Request $request password
    *
    * @return Response success message
    */
  public function delete(Request $request)
  {
    $user = request()->user();
    $hasher = app('hash');
    if ($hasher->check($request['password'], $user->password)) {
      $request->user()->token()->revoke();

      if ($user->delete()) {
        return response()->json([
          'success' => true,
          'message' => 'Your account has been successfully deleted',
        ]);
      }

      return response()->json([
        'success' => false,
        'message' =>'Failed to delete your account!',
      ]);
    }

    return response()->json([
      'success' => false,
      'message' =>'Incorrect Password',
    ]);
  }


  /**
    * Return a positive response if the user can access this method
    *
    * @return Response
    */
  public function verificationCheck(){
    return response()->json([
      'success' => true,
      'message' => 'Authorised',
    ]);
  }


  /**
    * Show a list of all of the application's users.
    *
    * @return Response success User
    */
  public function getAllOtherUsers(Request $request)
  {
    // TODO: check users are friends
    // $users = auth()->user()->getAllFriendships();

    $user = $request->user();

    $prefGenders = $user->prefGenders;
    $prefCities = $user->prefCities;
    $prefMaritalStatues = $user->prefMaritalStatuses;

    $genders = array();
    $cities = array();
    $maritalStatuses = array();

    foreach ($prefGenders as $prefGender){
      $genders[] = $prefGender->gender_id;
    }

    foreach ($prefCities as $prefCity){
      $cities[] = $prefCity->city_id;
    }

    foreach ($prefMaritalStatues as $prefMaritalStatus){
      $maritalStatuses[] = $prefMaritalStatus->marital_status_id;
    }

    $users = collect(User::where('id', '!=', $user->id)
                  // ->where('adminApproved', 1)
                  ->where('adminBanned', 0)
                  ->where('numOfChildren', '<=', $user->prefMaxNumOfChildren)
                  ->whereIn('gender_id', $genders)
                  ->whereIn('city_id', $cities)
                  ->whereIn('marital_status_id', $maritalStatuses)
                  ->get()
                  ->whereBetween('age', [$user->prefMinAge, $user->prefMaxAge])
                  )
                  ->forPage($request->pageNum, 20)
                  ->values()
                  ->all()
                  ;

    if(count($users) === 0)
      return response()->json([
        'success' => false,
        'message' => 'No Other Users Have Been Approved Yet'
      ]);    

    return response()->json([
      'success' => true,
      'data' => $users,
    ]);
  }


  /**
    * Show a list of all of the application's users.
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
  public function getUserWithID(Request $request){
      $user = User::where('id', $request['id'])->first();

      if(is_null($user))
        return response()->json([
          'success' => false,
          'message' => 'User Not Found'
        ]);

      return response()->json([
        'success' => true,
        'data' => $user
      ]);
  }

  // TODO: Allow user to update prefrences without needing reapproval
  public function updatePreferences(Request $request){

  }
}