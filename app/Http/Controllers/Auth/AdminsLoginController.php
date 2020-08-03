<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Redirect;
use Auth;

class AdminsLoginController extends Controller
{

  use AuthenticatesUsers, ThrottlesLogins;

    public function __construct()
    {
      $this->middleware('guest:admin')->except(['logout']);
    }



    public function getLoginForm()
    {
      return view('auth.admin-login');
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */

    public function maxAttempts()
    {
      return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
    }


    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */

    public function decayMinutes()
    {
      return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 5;
    }

    /**
     * Validate form data
     * Check they have not failed too many times
     * Attempt to login the admin
     *
     * @var string
     */
    public function login(Request $request)
    {
      // Validate the form data
      $this->validate($request, [
        'email'   => 'required|email',
        'password' => 'required'
      ]);

      // Check the user has not failed too many login attempts 
      if ($this->hasTooManyLoginAttempts($request))
      {
        $this->fireLockoutEvent($request);
        return $this->sendLockoutResponse($request);
      }

      // Attempt to log the user in with form data
      if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember))      
      {
        // if successful, then redirect to their intended
        // location and clear failed login count
        $this->clearLoginAttempts($request);
        return redirect()->intended(route('admin.home'));
      }

      // if unsuccessful, then increment failed login counter
      // and redirect back to the login with the form data and error
      $this->incrementLoginAttempts($request);
      $errors = new MessageBag(['email' => ['These credentials do not match our records.']]);
      return Redirect::back()->withErrors($errors)->withInput($request->except('password'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        Auth::guard('admin')->logout();

        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }

}
