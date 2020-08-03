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

        // Check user is approved by admin's
        if($request->user()->adminApproved == 1){
            // Return the Route the User attempted to access
            return $next($request);
        }
        elseif($request->user()->adminBanned == 1)
        {
            if(!empty($request->user()->adminUnapprovedMessage))
                $message = $request->user()->adminUnapprovedMessage;
            else
                $message = "Banned by Admins";
            $code = 403;
        }
        else
        {
            if(!empty($request->user()->adminUnapprovedMessage))
                $message = $request->user()->adminUnapprovedMessage;
            else
                $message = "Awaiting Admin Approval";
            $code = 401;
        }

        // Return JSON message explaining lack of access
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
            ]);
    }
}
