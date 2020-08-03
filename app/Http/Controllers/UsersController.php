<?php

namespace App\Http\Controllers;

use Demency\Friendships\Traits\Friendable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use App\User;
use Redirect;



class UsersController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $userProf = Auth::user();
        return view("users/publicProfile", ['userProf' => $userProf]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = User::find(Auth::user()->id);
        Auth::logout();

        try{
            $user->prefCities()->detach();
            $user->prefGenders()->detach();
            $user->prefMaritalStatuses()->detach();
        }finally{
        
            if ($user->delete()) {
                session()->flash('status.level', 'success');
                session()->flash('status.message', 'Your account has been deleted!');
                return Redirect::route('welcome');
            }
        }
    }

}
