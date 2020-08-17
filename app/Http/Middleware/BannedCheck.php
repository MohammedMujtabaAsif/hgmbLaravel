<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class BannedCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $user = $request->user();

        if($user==null){
            $user = User::where('email', $request->email)->first()->makeVisible('adminBanned');
        }
        
        // Check user is banned
        if($user->adminBanned == 1){
            //Return a message explaining ban
            $type = "banned";
            $message = "Banned by Admins";
            if(!empty($user->adminBannedMessage)){
                $message = $user->adminBannedMessage;
            }
            // Return JSON message explaining lack of access
            return response()->json([
                'success' => false,
                'message' => $message,
                'type' => $type,
            ], 401);
        }

        // If they are not banned
        // Return the Route the User attempted to access
        return $next($request);
    }
}

