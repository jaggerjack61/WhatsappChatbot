@extends('layouts.master')

@section('content')
    <div class="p-5 bg-opacity-10" style="background-image:url('/img/login.jpg')">
    <div class="card m-5 justify-content-center align-items-center" style="height: 100%;opacity:0.95">
        <h1 class="card-title text-center">
            Login
        </h1>
        <h3>
        @if(session()->has('error'))
            <div class="bg-danger text-white">

                {{ session()->get('error') }}
            </div>
        @elseif(session()->has('success'))
            <div class="bg-success">

                {{ session()->get('success') }}
            </div>
        @endif</h3>
    <form  action="{{route('login')}}" method="post">
        @csrf
        <div class="form-group">
            <label for="inputEmail">Email</label>
            <input type="text" name="email" class="form-control" id="inputEmail" placeholder="Email">
        </div>
        <div class="form-group">
            <label for="inputPassword">Password</label>
            <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
        </div>
        <button type="submit" class="btn btn-primary m-1">Sign in</button>
    </form>
    </div>
    </div>
@endsection
