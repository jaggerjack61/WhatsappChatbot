<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function showUsers()
    {
        $users=User::all();
        return view('settings.users',compact('users'));
    }

    public function saveUser(Request $request)
    {
        //dd($request->userLevel);
        try{
            if($request->password==$request->passwordConfirmation){
                User::create([
                    'name'=>$request->name,
                    'access_level'=>$request->userLevel,
                    'email'=>$request->email,
                    'password'=>$request->password,

                ]);
                return back()->with('success', 'User has been saved successfully');
            }
            else{
                return back()->with('error', 'Passwords do not match.');
            }

        }
        catch(\Exception $e){
            return back()->with('error',$e->getMessage());
        }

    }

    public function updateUser(Request $request)
    {
        $user = User::find($request->userId);
        //dd($user,$request->userId);
        $user->update(['name'=>$request->name,'email'=>$request->email]);
        $user->save();
        return back()->with('success', 'User has been updated successfully');
    }

    public function promote(User $user)
    {
        $user->update(['access_level'=>'admin']);
        $user->save();
        return back()->with('success', $user->name.' has been promoted successfully');
    }
    public function demote(User $user)
    {
        $user->update(['access_level'=>'user']);
        $user->save();
       // dd($user);
        return back()->with('success', $user->name.' has been demoted successfully');
    }

    public function activate(User $user)
    {
        $user->update(['status'=>'active']);
        $user->save();

        return back()->with('success', $user->name.' has been activated successfully');
    }
    public function deactivate(User $user)
    {
        $user->update(['status'=>'inactive']);
        $user->save();

        return back()->with('success', $user->name.' has been deactivated successfully');
    }

    public function showProfile()
    {
        $user=auth()->user();
        return view('settings.profile', compact('user'));
    }
}
