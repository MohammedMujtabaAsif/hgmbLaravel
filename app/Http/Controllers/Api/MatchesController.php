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
    * Get friend requests requests from other users
    *
    *
    * @return Response
    */
    public function getIncomingFriendRequests(){
        $friendRequests = request()->user()->getFriendRequests(10);
        
        $senders = array();
        
        // extract the sender from the list of friend requests
        foreach($friendRequests as $friendRequest){
            $sender = $friendRequest->sender;
            $senders[] = $sender;
        }

        // check there are no incoming requests
        if(count($senders) === 0){
            // if there are no incoming requests, return unsucessful message
            return response()->json([
                'success' => false,
                'message' => 'No Incoming Match Requests',
            ]);
        }
        
        // return positive message with list of incoming match requests
        return response()->json([
            'success' => true,
            'data' => $senders
        ]);
    }


    /**
    * Get friend requests requests sent to other users
    *
    *
    * @return Response
    */
    public function getOutgoingFriendRequests(){
        $friendRequests = request()->user()->getPendingFriendships(10);
        $id = request()->user()->id;

        $recipients = array();

        // for each friendRequest, get the recipient,
        // where the recipient is not the auth user
        foreach($friendRequests as $friendRequest){
            if($friendRequest->recipient->id!=$id){
                $recipients[] = $friendRequest->recipient;
            }
        }
        
        // check there are no recipients
        if(count($recipients) == 0){
            // if there are no recipients, return unsuccessful message
            return response()->json([
                'success' => false,
                'message' => 'No Outgoing Match Requests',
            ]);
        }
        
        // return positive message with list of outgoing friend requests        
        return response()->json([
            'success' => true,
            'data' => $recipients
        ]);
    }


    /**
    * Get users matched with authenticated user
    *
    * @return Response
    */
    public function getAcceptedFriendRequests(){
        $friends = request()->user()->getFriends(10);

        // check there are no friends found
        if(count($friends) == 0)
            // if no friends are found, return unsuccessful message
            return response()->json([
                'success' => false,
                'message' => 'No Matches',
            ]);

        // if friend requests are found return them as JSON        
        return response()->json([
            'success' => true,
            'data' => $friends
        ]);
    }


    /**
    * Return users the current user has denied matches from
    *
    *  @return Response deniedUsers
    */
    public function getDeniedFriendRequests(){
        $deniedRequests = request()->user()->getDeniedFriendships(10);
        $id = request()->user()->id;

        $deniedUsers = array();
        
        // for each deniedmatch, get the sender of the match
        // where the sender is not the auth user
        foreach($deniedRequests as $deniedRequest){
            if($deniedRequest->sender->id != $id){
                $deniedUsers[] = $deniedRequest->sender;
            }
        }

        // check there are no deniedUsers
        if(count($deniedUsers) == 0)
            // if there no deniedUsers, return unsuccessful message
            return response()->json([
                'success' => false,
                'message' => "No Denied Matches Found"
            ]);

        //return positive message with list of deniedUsers
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
    public function getBlockedFriends(){
        $blockedFriends = request()->user()->getBlockedFriendships(10);
        $id = request()->user()->id;

        $blockedUsers = array();

        // for each blocked user, retrieve the recipient
        // where the recipient isn't the auth user
        foreach($blockedFriends as $blockedFriend){
            if($blockedFriend->recipient->id != $id){
                $blockedUsers[] = $blockedFriend->recipient;
            }
        }

        // check there no blockedUsers
        if(count($blockedUsers) == 0)
            // if there no blocked users, return unsuccessful message
            return response()->json([
                'success' => false,
                'message' => "You Have Not Blocked Anyone"
            ]);
        
        // return positive message with list of users blocked by auth user
        return response()->json([
            'success' => true,
            'data' => $blockedUsers
        ]);
    }


    /**
    * Send friend request request to another user
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
    public function sendFriendRequest(Request $request)
    {
        $user = $request->user();
        $recipient = User::where('id', $request['id'])->first();
        
        // check user was found
        if(is_null($recipient))
            // if they could not be found, return unsuccessful response
            return response()->json([
                'success' => false,
                'message' => 'Could Not Find User'
            ]);

        // check user is blocked by recipient
        if($user->isBlockedBy($recipient)){
            // return fail message explaining problem
            return response()->json([
                'success' => false,
                'message' => $recipient->prefName . ' has Blocked You',
            ]);
        }

        // check user has a friend request from recipient    
        elseif($user->hasFriendRequestFrom($recipient)){
            // if user has pending request from recipient, accept it
            if($user->acceptFriendRequest($recipient)){
                // if friend request could be accepted, return successul message
                return response()->json([        
                    'success' => true,
                    'message' => 'You Matched with ' . $recipient->prefName,
                ]);
            }             
        }
        
        // check user has already sent a match request to recipient
        elseif($user->hasSentFriendRequestTo($recipient)){
            // if user has sent request already, return fail message explaining problem
            return response()->json([
                'success' => false,
                'message' => 'You Have Already Sent a Match Request to ' . $recipient->prefName,
            ]);
        }

        // check match sender is not already matched with recipient               
        elseif($user->isFriendWith($recipient)){
            // if users are friends, return fail message explaining problem
            return response()->json([
                'success' => false,
                'message' => 'Already Matched with ' . $recipient->prefName,
            ]);
        }

        // attempt to send friend request to recipient
        elseif($user->befriend($recipient)){
            // if friend request sent, return success message
            return response()->json([        
                'success' => true,
                'message' => 'Match Request Sent to ' . $recipient->prefName,
            ]);
        }
        
        // if all conditions fail, return generic fail message
        return response()->json([
            'success' => false,
            'message' => 'Unable to Send Match Request to ' . $recipient->prefName,
        ]);
    }


    /**
    * Accept a friend request from another user
    *
    * @param Request $request id non-authed user
    *
    * @return Response
    */
    public function acceptFriendRequest(Request $request){
        $user = $request->user();
        $sender = User::where('id', $request['id'])->first();
        
        // check user was found
        if(is_null($sender))
            // if they could not be found, return unsuccessful response
            return response()->json([
                'success' => false,
                'message' => 'Could Not Find User'
            ]);
        
        // check user has a friend request from sender
        if($user->hasFriendRequestFrom($sender)){
            // if friend requests exists, accept it
            if($user->acceptFriendRequest($sender)){
                // if friend request was accepted, return positive response
                return response()->json([
                    'success' => true,
                    'message' => 'Accepted Match Request from ' . $sender->prefName,
                ]);
            }
            // if friend request could not be accepted, return unsuccessful response
            return response()->json([
                'success' => false,
                'message' => 'Failed to Accept Match Request from ' . $sender->prefName,
            ]);
        }
        
        // if user did not have friend request from sender, return unsuccessful message
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
    public function denyFriendRequest(Request $request){
        $user = $request->user();
        $sender = User::where('id', $request['id'])->first();
        
        // check user was found
        if(is_null($sender))
            // if they could not be found, return unsuccessful response
            return response()->json([
                'success' => false,
                'message' => 'Could Not Find User'
            ]);
        
        // check user has a friend request from sender
        if($user->hasFriendRequestFrom($sender)){
            // if friend requests exists, deny it
            if($user->denyFriendRequest($sender)){
                // if friend request was denied, return positive response
                return response()->json([
                    'success' => true,
                    'message' => 'Denied Match Request from ' . $sender->prefName,
                ]);
            }

            // if friend request could not be denied, return unsucessful message
            return response()->json([
                'success' => false,
                'message' => 'Failed to Deny Match Request from ' . $sender->prefName,
            ]);
        }
        
        // if user did not have friend request from sender, return unsuccessful message
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
    public function unfriend(Request $request){
        $user = $request->user();
        $friend = User::where('id', $request['id'])->first();
        
        // check user was found
        if(is_null($friend))
            // if they could not be found, return unsuccessful response
            return response()->json([
                'success' => false,
                'message' => 'Could Not Find User'
            ]);
        
        // check users are friends
        if(($user->isFriendWith($friend))){
            // if users are friends, unfriend them
            if($user->unfriend($friend)){
                // if users were unfriended, return successful response
                return response()->json([        
                    'success' => true,
                    'message' => 'Unmatched with ' . $friend->prefName
                ]);
            }
            
            // if users were not unfriended, return unsuccessful response
            return response()->json([        
                'success' => false,
                'message' => 'Failed to Unmatch with ' . $friend->prefName
            ]);
        }
        
        // if users are not friends, return unsuccessful response
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
    public function blockFriend(Request $request){
        $user = $request->user();
        $userToBlock = User::where('id', $request['id'])->first();
        
        // check userToBlock was found
        if(is_null($userToBlock))
            // if they could not be found, return unsuccessful response
            return response()->json([
                'success' => false,
                'message' => 'Could Not Find User'
            ]);
        

        // check user hasn't blocked the userToBlock
        if(!$user->hasBlocked($userToBlock) ){
            // attempt to block userToBlock
            if($user->blockFriend($userToBlock)){
                // if it was successful, return successful response
                return response()->json([        
                    'success' => true,
                    'message' => 'Blocked ' . $userToBlock->prefName,
                ]);
            }

            // return unsuccessful, return response stating userToBlock was not blocked   
            return response()->json([
                'success' => false,
                'message' => 'Unable to Block ' . $userToBlock->prefName
            ]);
        }
    
        // if userToBlock is already blocked, return unsuccessful message
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
    public function unblockFriend(Request $request){
        $user = $request->user();
        $userToUnblock = User::where('id', $request['id'])->first();
        

        // check user was found
        if(is_null($userToUnblock))
            // if they could not be found, return unsuccessful response
            return response()->json([
                'success' => false,
                'message' => 'Could Not Find User'
            ]);
        
        // check user has blocked the userToUnblock
        if($user->hasBlocked($userToUnblock)){
            // attempt to unblock the userToUnblock
            if($user->unblockFriend($userToUnblock)){
                // if unblock was successful, return a successful response
                return response()->json([        
                    'success' => true,
                    'message' => 'Unblocked ' . $userToUnblock->prefName,
                ]);
            }
            // return unsuccessful response if user couldn't be unblocked
            return response()->json([
                'success' => false,
                'message' => 'Unable to Unblock ' . $userToUnblock->prefName,
            ]);
        }
        
        // return unsuccessful message if user was not blocked
        return response()->json([
            'success' => false,
            'message' => $userToUnblock->prefName . ' is Not Blocked',
        ]);
    }
}
