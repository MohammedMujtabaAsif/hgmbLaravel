<?php

namespace App\Http\Middleware;

use Closure;

class ApprovedCheck
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
        // Check user is banned
        if($request->user()->adminBanned == 1){
            //Return a message explaining ban
            $code = 401;
            $type = "banned";
            $message = "Banned by Admins";
            if(!empty($request->user()->adminBannedMessage))
                $message = $request->user()->adminBannedMessage;
        }
        // Check user is approved by admin's
        elseif($request->user()->adminApproved == 1){
            // Return the Route the User attempted to access
            return $next($request);
        }
        // if not banned or approved, User must be unapproved
        else{
            // Return message explaining uapproval
            $code = 402;
            $type = "unapproved";
            $message = "Awaiting Admin Approval";
            if(!empty($request->user()->adminUnapprovedMessage))
                $message = $request->user()->adminUnapprovedMessage;
        }

        // Return JSON message explaining lack of access
        return response()->json([
            'success' => false,
            'message' => $message,
            'type' => $type,
        ], $code);
    }
}
