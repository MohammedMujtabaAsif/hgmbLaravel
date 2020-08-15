<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Demency\Friendships\Traits\Friendable;
use Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Admin;
use App\User;

class AdminsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admins/admin');
    }



    /**
     * Get all registered Users
     *
     * @return App\User
     *
     *
     *    
     */
    public function getApprovedUsers()
    {
        $approvedUsers = User::where('adminApproved', 1)->where('adminBanned',  0)->orderBy('firstNames')->paginate(15);
        return view ('admins/users', ['users' => $approvedUsers, 'title' => 'Approved Users']);
    }


    /**
     * Get all registered Users
     *
     * @return App\User
     *
     *
     *    
     */
    public function getUnapprovedUsers()
    {
        $unapprovedUsers = User::where('adminApproved', 0)->where('adminBanned', 0)->where('email_verified_at', '!=', NULL)->orderBy('updated_at', 'asc')->paginate(15);
        return view ('admins/users', ['users' => $unapprovedUsers, 'title' => 'Unapproved Users']);
    }

    /**
     * Get all registered Users
     *
     * @return App\User
     *
     *
     *    
     */
    public function getBannedUsers()
    {
        $bannedUsers = User::where('adminBanned', 1)->orderBy('updated_at')->paginate(15);
        return view ('admins/users', ['users' => $bannedUsers, 'title' => 'Banned Users']);
    }





    /**
    * Get a specific User with Id
    *
    * @return App\User
    *
    *
    *    
    */
    public function getUser($id)
    {
        $user = User::where('id', $id)->first();
        return view ('admins/user')->with(['userProf' => $user]);
    }


    public function approveUser($id){

        
        $user = User::findOrFail($id);
        $user->adminApproved = 1;
        $user->adminUnapprovedMessage = null;
        $user->save();

        session()->flash('status.level', 'success');
        session()->flash('status.message', $user->firstNames.' '.$user->surname.' '.'Approved');
        return back();        
        return redirect()->route('admin.user', ['id' => $id]);

    }


    public function unapproveUser(Request $request, $id){

        
        $this->validate($request,
        [
            'adminUnapprovedMessage' => 'required',
        ]);
        $user = User::findOrFail($id);
        $user->adminApproved = 0;
        if(request()->adminUnapprovedMessage!=null)
            $user->adminUnapprovedMessage = request()->adminUnapprovedMessage;
        $user->save();

        session()->flash('status.level', 'warning');
        session()->flash('status.message', $user->firstNames.' '.$user->surname.' '.'Unapproved');        
        return back();        
        return redirect()->route('admin.user', ['id' => $id]);

    }


    // public function addUnapprovalMessage($id){

    //     $user = User::findOrFail($id);
    //     $user->adminUnapprovedMessage = request()->adminUnapprovedMessage;
    //     $user->save();

    //     session()->flash('status.level', 'success');
    //     session()->flash('status.message', 'Message sent to '.$user->firstNames.' '.$user->surname);
    //     return back();        
    //     return redirect()->route('admin.user', ['id' => $id]);

    // }


    public function banUser(Request $request, $id){

        $this->validate($request,
        [
            'adminBannedMessage' => 'required',
        ]);
        $user = User::findOrFail($id);
        $user->adminBanned = 1;
        $user->adminBannedMessage = request()->adminBannedMessage;
        $user->save();

        session()->flash('status.level', 'danger');
        session()->flash('status.message', $user->firstNames.' '.$user->surname.' '.'Banned');        
        return redirect()->back();        
        return redirect()->route('admin.user', ['id' => $id]);

    }


    public function unbanUser($id){

        $user = User::findOrFail($id);
        $user->adminBanned = 0;
        $user->adminBannedMessage = null;
        $user->save();

        session()->flash('status.level', 'success');
        session()->flash('status.message', $user->firstNames.' '.$user->surname.' '.'Unbanned');        
        return back();        
        return redirect()->route('admin.user', ['id' => $id]);
    }


    public function deleteUser($id){
        
        $user = User::findOrFail($id);
        $name = $user->firstNames.' '.$user->surname;
        $user->delete();

        session()->flash('status.level', 'danger');
        session()->flash('status.message', $name.' '.'Deleted');
        return redirect()->route('admin.bannedUsers');

    }


    public function getSchedule(){

    }


    public function getAdmins(){

    }


    public function deleteAdmin(){

    }
}