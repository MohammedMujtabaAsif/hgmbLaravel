<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Demency\Friendships\Traits\Friendable;
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
    $users = User::where('id', '!=', auth()->id())->where('adminApproved', 1)->where(auth()->user()->isFriendWith(),false)->get();
    foreach($users as $user){
      if($user->isFriendWith(auth()->user())){

      }

    }

    return response()->json($users);
  }


  /**
    * Get the authenticated User
    *
    * @return Response
    */
  public function currentUser(Request $request)
  {   $user = $request->user();
      $userDetails = $user
      ->only([
        'id',
        'firstNames',
        'surname',
        'prefName',
        'email',
        'phoneNumber',
        'dob',
        'age',
        'numOfChildren',
        'bio'])
      ;

      $prefCities = $user->prefCities->pluck('name');
      $prefGenders = $user->prefGenders->pluck('name');
      $prefMaritalStatuses = $user->prefMaritalStatuses->pluck('name');

    return response()->json([
      'user' => $userDetails,
      'prefCities' => $prefCities,
      'prefGenders' => $prefGenders,
      'prefMaritalStatuses' => $prefMaritalStatuses,
    ]);
  }


  /**
    * Get users matched with authenticated user
    * @param Request $request
    * @return Response
    */
  public function getMatchedUsers(Request $request){
    $user = $request->user();
    $friends = $user->getFriends();
    
    // if matches are found return them as JSON
    if($friends === [])
      return response()->json($friends);
    // if no matches are found, return a message 
    else
      return response()->json(['message' => 'No Matches']);
  }




  /**
    * Send match request to another user
    * @param Request $request
    * @return Response
    */
  public function sendMatchRequest(Request $request)
  {
    $user = $request->user();
    $recipient = User::where('id', $request('id'))->first();

    //Check match sender is not already matched with send
    if($user->isFriendWith($recipient)){
      return response()->json([
        'success' => false,
        'message' => 'Already Matched with User'
      ]);
    }

    if($user->befriend($recipient)!=false){
      return response()->json([        
        'success' => true,
        'message' => 'Friend Request Sent'
      ]);
    }
    else {
      return response()->json([
        'success' => false,
        'message' => 'Unable to Send Friend Request'
      ]);
    }
  }


  /**
    * Get match requests from other users
    * @param Request $request
    * @return Response
    */
  public function getMatchRequests(Request $request){
    $matchRequests = $request->user()->getFriendRequests();

    if(empty($matchRequests)){
      return response()->json([
        'message' => 'No matches found',
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

    return response()->json($user->acceptFriendRequest($sender));
  }


  /**
    * Deny a match request from another user
    * @param Request $request
    * @return Response
    */
  public function denyMatchRequest(Request $request){
    $user = $request->user();
    $sender = User::where('id', request('id'))->first();

    return response()->json($user->denyFriendRequest($sender));
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
      $user->unfriend($friend);
      return response()->json([        
        'success' => true,
        'message' => 'Match Ended'
      ]);
    }
    else {
      return response()->json([
        'success' => false,
        'message' => 'Unable to Unmatch'
      ]);
    }
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
    else {
      return response()->json([
        'success' => false,
        'message' => 'Unable to Block'
      ]);
    }
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
        'message' => 'User Unblocked.'
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