<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $arr=array('name'=>'samuel','age'=>13,'details'=>['sex'=>'male','extra_details'=>['mood'=>'happy']]);
        if(array_key_exists('mood',$arr['details']['extra_details'])){
            dd('it exists bitch');
        }
        return view('welcome');
    }
}
