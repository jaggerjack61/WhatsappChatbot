@extends('layouts.base')

@section('content-meta')

    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                @auth()
                <a class="navbar-brand" href="#">Virl MicroFinance</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse d-flex justify-content-end" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-link active" aria-current="page" href="{{route('show-dashboard')}}">Dashboard</a>
                        @if(auth()->user()->access_level=='admin')
                        <a class="nav-link" href="{{route('show-users')}}">Users</a>
                        @endif
                        <a class="nav-link" href="{{route('show-profile')}}">Edit Profile</a>
                        <a class="nav-link" href="{{route('logout')}}">Logout</a>

                    </div>



                </div>
                @endauth
            </div>
        </nav>
        @yield('content')
    </div>
    @yield('scripts')
@endsection

