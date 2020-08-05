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


  // /**
  //  * Create user
  //  *
  //  * @param  [string] name
  //  * @param  [string] email
  //  * @param  [string] password
  //  * @param  [string] password_confirmation
  //  * @return [string] message
  //  */
  // public function register(Request $request)
  //     {
  //         $validator = Validator::make($request->all(), [
  //         //Validate user's personal details
  //         'firstNames' => 'required|string',
  //         'surname' => 'required|string',
  //         'prefName'=>'required|string',
  //         'email' => 'required|string|email|unique:users',
  //         'password' => 'required|string|confirmed',
  //         'phoneNumber' => 'required|unique:users|max:11|regex:/(0)[0-9]{10}/',
  //         'gender'=>'required|int',
  //         'userCity' => 'required|int',
  //         'maritalStatus'=>'required|int',
  //         'dob'=>'required|date|before:18 years ago',
  //         'numOfChildren'=>'int',
  //         'bio'=>'required|string|max:1000',

  //         //Validate user's partner preferences
  //         // 'prefCities' => 'required|int|array|distinct',
  //         // 'prefGenders'=>'required|int|array|distinct',
  //         // 'prefMaritalStatuses'=>'required|int|array|distinct',
  //         // 'prefMinAge'=>'required|int|min:18|lt:prefMaxAge',
  //         // 'prefMaxAge'=>'required|int|min:20|gt:prefMinAge',
  //         // 'prefMaxNumOfChildren'=>'required|int',
  //         ]);

  //         if ($validator->fails()) {
  //             return response()->json([
  //             'success' => false,
  //             'message' => $validator->errors(),
  //             'code' => 400,
  //             ]);
  //         }

  //         $input = $request->all();
  //         $input['password'] = Hash::make($input['password']);            
  //         $input['age'] = Carbon::parse($input['dob'])->diff(Carbon::now())->format('%y');
  //         $user = User::create($input);

  //         $token['token'] = $user->createToken('appToken')->accessToken;

  //         // return verificationCheck($user, $token);
  //         return response()->json([
  //             'success' => true,
  //             'token' => $token,
  //             'user' => $user,
  //             'code' => 200,
  //         ]);
  //     }

  public function verificationCheck(){
    return response()->json([
      'success' => true,
      'message' => 'authorised',
    ]);
  }


  public function deleteAccount()
  {
    $user = User::find(Auth::user()->id);
    $user->logout();

    if ($user->delete()) {
      return response()->json([
        'success' => true,
        'message' => 'Your account has been successfully deleted',
        'code' => 200,
      ]);
    }

    else {
      return response()->json([
        'success' => false,
        'message' =>'Failed to delete your account!',
      ]);
    }
  }


  /**
    * Show a list of all of the application's users.
    *
    * @return Response
    */
  public function allOtherUsers()
  {

    $users = auth()->user()->getAllFriendships();

    //TODO: Make SELECT query using User's preferences
    $users = User::select([
        'id',
        'firstNames',
        'surname',
        'prefName',
        'email',
        'phoneNumber',
        'gender_id',
        'city_id',
        'marital_status_id',
        'dob',
        'age',
        'numOfChildren',
        'bio',
        'prefMinAge',
        'prefMaxAge',
        'prefMaxNumOfChildren',
      ])

      ->where('id', '!=', auth()->id())
      ->where('adminBanned', 0)

      ->with('gender')
      ->with('city')
      ->with('maritalStatus')
      ->with('prefCities')
      ->with('prefGenders')
      ->with('prefMaritalStatuses')
      ->paginate(15);

    return response()->json($users);
  }


  /**
    * Get the authenticated User
    *
    * @return Response
    */
  public function currentUser(Request $request)
  {   
    
    $user = $request->user();
    $userCleaned = $user->only([
        'id',
        'firstNames',
        'surname',
        'prefName',
        'email',
        'phoneNumber',
        'dob',
        'age',
        'numOfChildren',
        'bio',
        'prefMinAge',
        'prefMaxAge',
        'prefMaxNumOfChildren',
      ]);

    $userDetails = [
      // 'userDetails' => $userCleaned['id'],
      'id' => $userCleaned['id'],
      'firstNames' => $userCleaned['firstNames'],
      'surname' => $userCleaned['surname'],
      'prefName' => $userCleaned['prefName'],
      'email' => $userCleaned['email'],
      'phoneNumber' => $userCleaned['phoneNumber'],
      'dob' => $userCleaned['dob'],
      'age' => $userCleaned['age'],
      'numOfChildren' => $userCleaned['numOfChildren'],
      'bio' => $userCleaned['bio'],
      'prefMinAge' => $userCleaned['prefMinAge'],
      'prefMaxAge' => $userCleaned['prefMaxAge'],
      'prefMaxNumOfChildren' => $userCleaned['prefMaxNumOfChildren'],
      'gender' => $user->gender,
      'city' => $user->city,
      'marital_status' => $user->maritalStatus,
      'pref_cities' => $user->prefCities,
      'pref_genders' => $user->prefGenders,
      'pref_marital_statuses' => $user->prefMaritalStatuses,
      ];


    return response()->json(
      $userDetails,
      // $request->user()->city,
      // $request->user()->gender,
      // $request->user()->maritalStatus,
      // $request->user()->prefCities,
      // $request->user()->prefGenders,
      // $request->user()->prefMaritalStatuses,
    );
  }


  /**
    * Get users matched with authenticated user
    * @param Request $request
    * @return Response
    */
  public function getMatchedUsers(Request $request){
    $user = $request->user();
    $friends = $user->getFriends(10, 'simple');
    
    if(count($friends) === 0)
    // if no matches are found, return a message 
      return response()->json(['message' => 'No Matches']);
    else
    // if matches are found return them as JSON
      return response()->json([$friends]);
  }




  /**
    * Send match request to another user
    * @param Request $request
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
      $user->befriend($recipient);
      return response()->json([        
        'success' => true,
        'message' => 'You Matched with ' . $recipient->prefName,
      ]);
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
    * @param Request $request
    * @return Response
    */
  public function getMatchRequests(Request $request){
    $matchRequests = $request->user()->getFriendRequests($perPage = 10);

    if(count($matchRequests) === 0){
      return response()->json([
        'message' => 'No Matches Found',
      ]);
    }

    return response()->json($matchRequests);
  }


  /**
    * Accept a match request from another user
    * @param Request $request
    * @return Response
    */
  public function acceptMatchRequest(Request $request){
    $user = $request->user();
    $sender = User::where('id', request('id'))->first();

    if($user->acceptFriendRequest($sender)){
      return response()->json(['success' => true, 'message' => 'Accepted Match Request from ' . $sender->prefName], 200);
    }
    
    return response()->json(['success' => false, 'message' => 'Failed to Accept Match Request from ' . $sender->prefName]);

  }


  /**
    * Deny a match request from another user
    * @param Request $request
    * @return Response
    */
  public function denyMatchRequest(Request $request){
    $user = $request->user();
    $sender = User::where('id', request('id'))->first();

    if($user->denyFriendRequest($sender)){
      return response()->json(['success' => true, 'message' => 'Denied Match Request from ' . $sender->prefName], 200);
    }

    return response()->json(['success' => false, 'message' => 'Failed to Deny Match Request from ' . $sender->prefName]);
  }


  /**
    * Remove match with another user
    * @param Request $request
    * @return Response
    */
  public function unmatch(Request $request){
    $user = $request->user();
    $friend = User::where('id', request('id'))->first();

    if(($user->isFriendWith($friend))){

      if($user->unfriend($friend)){
        return response()->json([        
          'success' => true,
          'message' => 'Match Ended'
        ]);
      }

      return response()->json([        
        'success' => false,
        'message' => 'Failed to Unmatch'
      ]);
    }

    return response()->json([
      'success' => false,
      'message' => 'You must match with ' . $friend->prefName . ' before unmatching'
    ]);
    
  }


  /**
    * Block user from authenticated user
    * @param Request $request
    * @return Response
    */
  public function blockUser(Request $request){
    $user = $request->user();
    $userToBlock = User::where('id', request('id'))->first();

    if($user->blockFriend($userToBlock)){
      return response()->json([        
        'success' => true,
        'message' => 'User Blocked'
      ]);
    }

    return response()->json([
      'success' => false,
      'message' => 'Unable to Block'
    ]);
    
  }


  /**
    * Unblock user from authenticated user
    * @param Request $request
    * @return Response
    */
  public function unblockUser(Request $request){
    $user = $requst->user();
    $userToUnblock = User::where('id', request('id'))->first();

    if($user->unblockFriend($userToUnblock)){
      return response()->json([        
        'success' => true,
        'message' => 'User Unblocked'
      ]);
    }
    else {
      return response()->json([
        'success' => false,
        'message' => 'Unable to Unblock'
      ]);
    }
  }


  // public function storeProfileImage(Request $request){
  //   $image = $request->file('image');
  //   $user = $request->user();

  //   return response
  // }
}