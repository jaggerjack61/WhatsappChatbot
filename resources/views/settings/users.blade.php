@extends('layouts.master')

@section('content')
    @if(auth()->user()->access_level=='admin')
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

        <a href="#" class="btn btn-primary m-2" data-bs-toggle="modal" data-bs-target="#addUserModal">New User</a>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Access Level</th>
            <th>Status</th>
            <th>Action</th>

        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)

                <tr>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{strtoupper($user->access_level)}}</td>
                    <td>{{strtoupper($user->status)}}</td>

                    <td><button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editUserModal" onclick="
                    document.getElementById('userId').value='{{$user->id}}';
                    document.getElementById('editName').value='{{$user->name}}';
                    document.getElementById('editEmail').value='{{$user->email}}';">Edit</button>

                    @if ($user->access_level == 'admin')
                        <a href="{{route('demote',[$user->id])}}" class="btn btn-sm btn-danger">Demote to User</a>
                    @elseif($user->access_level == 'user')
                        <a href="{{route('promote',[$user->id])}}" class="btn btn-sm btn-success">Promote to Admin</a>
                    @endif

                    @if ($user->status == 'active')
                        <a href="{{route('deactivate',[$user->id])}}" class="btn btn-sm btn-danger">Deactivate</a>
                    @elseif($user->status == 'inactive')
                        <a href="{{route('activate',[$user->id])}}" class="btn btn-sm btn-success">Activate</a>
                    @endif
                        </td>
                </tr>
        @endforeach
        </tbody>
    </table>


        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('save-user')}}" method="post">
                            @csrf

                            <div class="form-group">
                                <label for="inputEmail">Email</label>
                                <input type="text" name="email" class="form-control" id="inputEmail" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label for="inputEmail">Name</label>
                                <input type="text" name="name" class="form-control" id="inputEmail" placeholder="Full name">
                            </div>
                            <div class="form-group">
                                <label for="inputPassword">Password</label>
                                <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label for="inputPassword">Password</label>
                                <input type="password" name="passwordConfirmation" class="form-control" id="inputPassword" placeholder="Confirm Password">
                            </div>

                            <button type="submit" class="btn btn-primary m-1">Save</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>


    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('update-user')}}" method="post">
                        @csrf
                        <input type="hidden" name="userId" class="form-control" id="userId">
                        <div class="form-group">
                            <label for="inputEmail">Email</label>
                            <input type="text" name="email" class="form-control" id="editEmail" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="inputEmail">Name</label>
                            <input type="text" name="name" class="form-control" id="editName" placeholder="Email">
                        </div>



                        <button type="submit" class="btn btn-primary m-1">Update</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @else
        <h2>You do not have permission to view this page.</h2>
    @endif

@endsection
