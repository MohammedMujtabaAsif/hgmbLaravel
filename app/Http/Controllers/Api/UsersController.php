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
use DatabaseRule;

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
      ->with('gender')
      ->with('city')
      ->with('maritalStatus')
      ->with('prefCities')
      ->with('prefGenders')
      ->with('prefMaritalStatuses')
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
      'password' => 'required|string|confirmed',
      'phoneNumber' => 'required|string|max:11|regex:/(0)[0-9]{10}/|unique:users,phoneNumber,' . $request->user()->id,
      'city_id' => 'required|integer',
      'gender_id'=>'required|integer',
      'marital_status_id'=>'required|integer',
      'dob'=>'required|date|before:18 years ago',
      'numOfChildren'=>'integer',
      'bio'=>'required|string|max:1000',
      'image' => 'file|max:5000',

      //Validate user's partner preferences
      'pref_cities' => 'required|array|distinct',
      'pref_cities.*' => 'integer',
      'pref_genders'=>'required|array|distinct',
      'pref_genders.*'=>'integer',
      'pref_marital_statuses'=>'required|array|distinct',
      'pref_marital_statuses.*'=>'integer',
      'pref_min_age'=>'required|integer|min:18',
      'pref_max_age'=>'required|integer|min:20|gt:pref_min_age',
      'pref_num_of_children'=>'required|integer',
    ]);

      if($validator->fails()){
        return response()->json([
          'success' => false,
          'message' => $validator->errors(),
          'email' => auth()->user()->email
        ]);
      }

      $user->update([
        // //User's personal details
        'firstNames' => $request['firstNames'],
        'surname' => $request['surname'],
        'prefName'=> $request['prefName'],
        'email' => $request['email'],
        'password' => bcrypt($request['password']),
        'phoneNumber' => $request['phoneNumber'],
        'city_id' => (int) $request['city_id'],
        'gender_id' => (int) $request['gender_id'],
        'marital_status_id' => (int) $request['marital_status_id'], 
        'dob' => Carbon::createFromFormat('Y-m-d', request['dob']),
        'age' => Carbon::parse($request['dob'])->diff(Carbon::now())->format('%y'),
        'numOfChildren' => $validator['numOfChildren'],
        'bio' => $request['bio'],
        // TODO: imageAddress

        //User's partner preferences
        'prefMinAge' => (int) $request['prefMinAge'],
        'prefMaxAge' => (int) $request['prefMaxAge'],
        'prefMaxNumOfChildren' => (int) $request['prefMaxNumOfChildren'],
      ]);


    if((int) $request['gender_id'] == 0){ 
      $prefGenders = 1;
    }
    else{
      $prefGenders = 0;
    }
    $user->prefGenders()->sync($prefGenders);
    
    $user->prefCities()->sync((int) $request['prefCities']);
    $user->prefMaritalStatuses()->sync((int) $request['prefMaritalStatuses']);

    return response()->json([
      'success' => true,
      'data' => $user
    ]);
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
      else {
        return response()->json([
          'success' => false,
          'message' =>'Failed to delete your account!',
        ]);
      }
    }
    else {        
      return response()->json([
        'success' => false,
        'message' =>'Incorrect Password',
      ]);
    }
  }


  /**
    * If the user can access the verified and approved middleware
    * Return a positive response
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
  public function getAllOtherUsers()
  {

    // $users = auth()->user()->getAllFriendships();
    //TODO: Make SELECT query using User's preferences

    $users = User::
        with('gender')
      ->with('city')
      ->with('maritalStatus')
      ->with('prefCities')
      ->with('prefGenders')
      ->with('prefMaritalStatuses')
      ->where('id', '!=', auth()->user()->id())
      ->where('adminApproved', 1)
      ->where('adminBanned', 0)
      ->paginate(10);

    if(count($users) === 0)
      return response()->json([
        'success' => false,
        'message' => 'No Other Users Have Been Approved Yet']);

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
      $user = User::
        with('gender')
      ->with('city')
      ->with('maritalStatus')
      ->with('prefCities')
      ->with('prefGenders')
      ->with('prefMaritalStatuses')
      ->where('id', $request['id'])->first();

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


  /**
    * Get users matched with authenticated user
    *
    * @return Response
    */
  public function getAcceptedMatches(){
    $friends = request()->user()->getFriends(10);
    
    if(count($friends) == 0)
    // if no matches are found, return a message 
      return response()->json([
        'success' -> false,
        'message' => 'No Matches',
      ]);

    // if matches are found return them as JSON
    foreach($friends as $friend){
      $friend->gender;
      $friend->city;
      $friend->maritalStatus;
      $friend->prefCities;
      $friend->prefGenders;
      $friend->prefMaritalStatuses;
    }

    return response()->json([
      'success' => true,
      'data' => $friends
    ]);
  }


  /**
    * Send match request to another user
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
  public function sendMatchRequest(Request $request)
  {
    $user = $request->user();
    $recipient = User::where('id', $request['id'])->first();

    //Check match sender is not already matched with send
    if($user->isFriendWith($recipient)){
      return response()->json([
        'success' => false,
        'message' => 'Already Matched with ' . $recipient->prefName,
      ]);
    }

    elseif($user->isBlockedBy($recipient)){
      return response()->json([
        'success' => false,
        'message' => $recipient->prefName . ' has Blocked You',
      ]);
    }

    elseif($user->hasSentFriendRequestTo($recipient)){
      return response()->json([
        'success' => false,
        'message' => 'You Have Already Sent a Match Request to ' . $recipient->prefName,
      ]);
    }

    elseif($user->hasFriendRequestFrom($recipient)){
      if($user->befriend($recipient)){
        return response()->json([        
          'success' => true,
          'message' => 'You Matched with ' . $recipient->prefName,
        ]);
      }
    }

    elseif($user->befriend($recipient)==true){
      return response()->json([        
        'success' => true,
        'message' => 'Match Sent to ' . $recipient->prefName,
      ]);
    }
    
    return response()->json([
      'success' => false,
      'message' => 'Unable to Send Match Request to ' . $recipient->prefName,
    ]);
    
  }


  /**
    * Get match requests from other users
    *
    *
    * @return Response
    */
  public function getPendingMatches(){
    $pendingMatches = request()->user()->getFriendRequests(10);

    if(count($pendingMatches) === 0){
      return response()->json([
        'success' => false,
        'message' => 'No Match Requests Found',
      ]);
    }
    
    $senders = array();

    foreach($pendingMatches as $matchRequest){
      $sender = $matchRequest->sender;
      $sender->gender;
      $sender->city;
      $sender->maritalStatus;
      $sender->prefCities;
      $sender->prefGenders;
      $sender->prefMaritalStatuses;
      $senders[] = $sender;
    }

    return response()->json([
      'success' => true,
      'data' => $senders
    ]);
  }


  public function getDeniedMatches(){
    $deniedMatches = request()->user()->getDeniedFriendships(10);
    
    if(count($deniedMatches) == 0)
      return response()->json([
        'success' => false,
        'message' => "No Denied Matches Found"
      ]);

    $senders = array();

    foreach($deniedMatches as $matchRequest){
      $sender = $matchRequest->sender;
      $sender->gender;
      $sender->city;
      $sender->maritalStatus;
      $sender->prefCities;
      $sender->prefGenders;
      $sender->prefMaritalStatuses;
      $senders[] = $sender;
    }

    return response()->json([
      'success' => true,
      'data' => $senders
    ]);
  }


  public function getBlockedMatches(){
    $blockedUsers = request()->user()->getBlockedFriendships(10);

    if(count($blockedUsers) == 0)
      return response()->json([
        'success' => false,
        'message' => "No Blocked Users Found"
      ]);

    return response()->json([
      'success' => true,
      'data' => $blockedUsers
    ]);
    
  }

  /**
    * Accept a match request from another user
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
  public function acceptMatchRequest(Request $request){
    $user = $request->user();
    $sender = User::where('id', $request['id'])->first();

    if(is_null($sender))
      return response()->json([
        'success' => false,
        'message' => 'Could Not Find User'
      ]);
    
    if($user->hasFriendRequestFrom($sender)){

      if($user->acceptFriendRequest($sender)){
        return response()->json([
          'success' => true,
          'message' => 'Accepted Match Request from ' . $sender->prefName,
        ]);
      }
      
      return response()->json([
        'success' => false,
        'message' => 'Failed to Accept Match Request from ' . $sender->prefName,
      ]);
    }

    return response()->json([
      'success' => false,
      'message' => 'There is No Match Request From ' . $sender->prefName,
    ]);    

  }


  /**
    * Deny a match request from another user
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
  public function denyMatchRequest(Request $request){
    $user = $request->user();
    $sender = User::where('id', $request['id'])->first();

    if(is_null($sender))
      return response()->json([
        'success' => false,
        'message' => 'Could Not Find User'
      ]);
    
    if($user->hasFriendRequestFrom($sender)){
      if($user->denyFriendRequest($sender)){
        return response()->json([
          'success' => true,
          'message' => 'Denied Match Request from ' . $sender->prefName,
        ]);
      }

      return response()->json([
        'success' => false,
        'message' => 'Failed to Deny Match Request from ' . $sender->prefName,
      ]);
    }

    return response()->json([
      'success' => false,
      'message' => 'There is No Match Request From ' . $sender->prefName,
    ]);
    
  }


  /**
    * Remove match with another user
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
  public function unmatch(Request $request){
    $user = $request->user();
    $friend = User::where('id', $request['id'])->first();

    if(is_null($friend))
      return response()->json([
        'success' => false,
        'message' => 'Could Not Find User'
      ]);

    if(($user->isFriendWith($friend))){

      if($user->unfriend($friend)){
        return response()->json([        
          'success' => true,
          'message' => 'Unmatched with ' . $friend->prefName
        ]);
      }

      return response()->json([        
        'success' => false,
        'message' => 'Failed to Unmatch with ' . $friend->prefName
      ]);
    }

    return response()->json([
      'success' => false,
      'message' => 'You Must Match With ' . $friend->prefName . ' Before Unmatching',
    ]);
    
  }


  /**
    * Block user from authenticated user
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
  public function blockMatch(Request $request){
    $user = $request->user();
    $userToBlock = User::where('id', $request['id'])->first();

    if(is_null($userToBlock))
      return response()->json([
        'success' => false,
        'message' => 'Could Not Find User'
      ]);
    
    if($user->hasBlocked($userToBlock) == false){

      if($user->blockFriend($userToBlock)){
        return response()->json([        
          'success' => true,
          'message' => 'Blocked ' . $userToBlock->prefName,
        ]);
      }

      return response()->json([
        'success' => false,
        'message' => 'Unable to Block'
      ]);
    }
    
    return response()->json([
      'success' => false,
      'message' => $userToBlock->prefName . ' is Already Blocked',
    ]);    
    
  }


  /**
    * Unblock user from authenticated user
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
  public function unblockMatch(Request $request){
    $user = $request->user();
    $userToUnblock = User::where('id', $request['id'])->first();

    if(is_null($userToUnblock))
      return response()->json([
        'success' => false,
        'message' => 'Could Not Find User'
      ]);

    if($user->hasBlocked($userToUnblock)){

      if($user->unblockFriend($userToUnblock)){
        return response()->json([        
          'success' => true,
          'message' => 'Unblocked ' . $userToUnblock->prefName,
        ]);
      }
      else {
        return response()->json([
          'success' => false,
          'message' => 'Unable to Unblock ' . $userToUnblock->prefName,
        ]);
      }
    }

    return response()->json([
      'success' => false,
      'message' => $userToUnblock->prefName . ' is Not Blocked',
    ]);    
  }



  // public function storeProfileImage(Request $request){
  //   $image = $request->file('image');
  //   $user = $request->user();

  //   return response
  // }
}