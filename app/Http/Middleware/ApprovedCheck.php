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
        // check user is unapproved by admins
        if($request->user()->adminApproved == 0){
            // Setup message explaining unapproval

            // Setup a default message
            $message = "Awaiting Admin Approval";

            // Replace with admin message if it exists
            if(!empty($request->user()->adminUnapprovedMessage))
                $message = $request->user()->adminUnapprovedMessage;

            // Return JSON message explaining lack of access
            return response()->json([
                'success' => false,
                'message' => $message,
                'type' => "unapproved",
            ], 402);
        }

        // Allow access if the user is approved
        return $next($request);

    }
}
