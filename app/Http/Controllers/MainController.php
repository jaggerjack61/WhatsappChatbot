<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $arr=array('name'=>'samuel','age'=>13,'details'=>['sex'=>'male','extra_details'=>['mood'=>'happy']]);
        if(array_key_exists('mood',$arr['details']['extra_details'])){
            dd('it exists bit');
        }
        return view('welcome');
    }

    public function showDashboard()
    {
        return view('pages.dashboard');
    }
    public function showPolicy()
    {
        return view('pages.policy');
    }
}
