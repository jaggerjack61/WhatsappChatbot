@extends('layouts.master')

@section('content')
    <h3>
        @if(session()->has('error'))
            <div class="bg-danger text-white" id="notice">

                {{ session()->get('error') }}
            </div>
            <script>
                await new Promise(r => setTimeout(r, 5000));
                document.getElementById('notice').style.display = 'none';
            </script>
        @elseif(session()->has('success'))
            <div class="bg-success" id="notice">

                {{ session()->get('success') }}

            </div>
            <script>
                await new Promise(r => setTimeout(r, 5000));
                document.getElementById('notice').style.display = 'none';
            </script>
        @endif</h3>
        <h1>Details</h1>
        <card>
            <form action="{{route('update-user')}}" method="post">
                @csrf
                <input type="hidden" name="userId" value="{{$user->id}}"/>
                <div class="form-group">
                    <label for="inputEmail">Email</label>
                    <input type="text" name="email" value="{{$user->email}}" class="form-control" id="inputEmail" placeholder="Email">
                </div>
                <div class="form-group">
                    <label for="inputEmail">Name</label>
                    <input type="text" name="name" class="form-control" value="{{$user->name}}" id="inputEmail" placeholder="Email">
                </div>



                <button type="submit" class="btn btn-primary m-1">Save</button>
            </form>
        </card>

@endsection
