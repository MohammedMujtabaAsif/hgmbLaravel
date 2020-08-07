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

class MatchesController extends Controller
{
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
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to Unblock ' . $userToUnblock->prefName,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => $userToUnblock->prefName . ' is Not Blocked',
        ]);
    }
}
