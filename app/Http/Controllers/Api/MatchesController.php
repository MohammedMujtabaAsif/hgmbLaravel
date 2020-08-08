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
                'success' => false,
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
                'message' => 'Match Request Sent to ' . $recipient->prefName,
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

        if(count($senders) === 0){
            return response()->json([
                'success' => false,
                'message' => 'No Match Requests Found',
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $senders
        ]);
    }

    /**
    * Get match requests sent to other users
    *
    *
    * @return Response
    */
    public function getSentRequests(){
        $pendingRequests = request()->user()->getPendingFriendships(10);


        $recipients = array();
        
        foreach($pendingRequests as $matchRequest){
            $recipient = $matchRequest->recipient;
            $recipient->gender;
            $recipient->city;
            $recipient->maritalStatus;
            $recipient->prefCities;
            $recipient->prefGenders;
            $recipient->prefMaritalStatuses;
            $recipients[] = $recipient;
        }
        
        if(count($recipients) === 0){
            return response()->json([
                'success' => false,
                'message' => 'No Pending Matches',
            ]);
        }
        
        
        return response()->json([
            'success' => true,
            'data' => $recipients
        ]);
    }


    /**
    * Return users the current user has denied matches from
    *
    *  @return Response deniedUsers
    */
    public function getDeniedMatches(){
        $deniedMatches = request()->user()->getDeniedFriendships(10);
        
        $deniedUsers = array();
        
        foreach($deniedMatches as $deniedMatch){
            $deniedUser = $deniedMatch->sender;
            if($deniedUser->id != request()->user()->id){
                $deniedUser->gender;
                $deniedUser->city;
                $deniedUser->maritalStatus;
                $deniedUser->prefCities;
                $deniedUser->prefGenders;
                $deniedUser->prefMaritalStatuses;
                $deniedUsers[] = $deniedUser;
            }
        }

        if(count($deniedUsers) == 0)
            return response()->json([
                'success' => false,
                'message' => "No Denied Matches Found"
            ]);

        return response()->json([
            'success' => true,
            'data' => $deniedUsers
        ]);
    }


    /**
    * Return users the current user has blocked
    *
    *  @return Response blockedUsers
    */
    public function getBlockedMatches(){
        $blockedUsers = request()->user()->getBlockedFriendships(10);

        $blocked = array();

        foreach($blockedUsers as $blockedUser){
            $recipient = $blockedUser->recipient;
            if($recipient->id != request()->user()->id){
                $recipient->gender;
                $recipient->city;
                $recipient->maritalStatus;
                $recipient->prefCities;
                $recipient->prefGenders;
                $recipient->prefMaritalStatuses;
                $blocked[] = $recipient;
            }
        }

        
        if(count($blocked) == 0)
        return response()->json([
            'success' => false,
            'message' => "You Have Not Blocked Anyone"
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $blocked
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
