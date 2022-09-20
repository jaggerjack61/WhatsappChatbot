<div>
    <div class="p-5">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Approved Users</button>
                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Pending Users</button>
                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile1" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Denied Users</button>
                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Approved Loans</button>
                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact1" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Pending Loans</button>
                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact2" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Denied Loans</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            @if($client->status == 'registered')
                            <tr>
                                <td>{{$client->name}}</td>
                                <td>{{$client->phone_no}}</td>
                                <td>{{strtoupper($client->status)}}</td>
                                <td>{{$client->handler->name}}</td>
                                <td><a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewClientModal" onclick="
                                    loadImages('{{$client->phone_no}}')">View</a>
                                    <a href="#" wire:click="deny('{{$client->id}}')" class="btn btn-sm btn-danger">Un-register</a></td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clients as $client)
                        @if($client->status == 'pending')
                            <tr>
                                <td>{{$client->name}}</td>
                                <td>{{$client->phone_no}}</td>
                                <td>{{strtoupper($client->status)}}</td>
                                <td><a href="#" wire:click="register('{{$client->id}}')" class="btn btn-sm btn-success">Register</a>
                                    <a href="#" wire:click="deny('{{$client->id}}')" class="btn btn-sm btn-danger">Deny</a>
                                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewClientModal" onclick="
                                    loadImages('{{$client->phone_no}}')">View</a>

                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-profile1" role="tabpanel" aria-labelledby="nav-profile-tab">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Status</th>
                        <th>Denied By</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clients as $client)
                        @if($client->status == 'denied')
                            <tr>
                                <td>{{$client->name}}</td>
                                <td>{{$client->phone_no}}</td>
                                <td>{{strtoupper($client->status)}}</td>
                                <td>{{$client->handler->name}}</td>
                                <td><a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewClientModal" onclick="
                                    loadImages('{{$client->phone_no}}')">View</a>
                                    <a href="#" wire:click="register('{{$client->id}}')" class="btn btn-sm btn-success">Register</a></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Approved By</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($loans as $loan)
                        @if($loan->status == 'approved')
                            <tr>
                                <td>{{$loan->owner->name}}</td>
                                <td>{{$loan->amount.' '.$loan->currency}}}</td>
                                <td>{{strtoupper($loan->due_date)}}</td>
                                <td>{{strtoupper($loan->handle_by)}}</td>
                                <td><a href="#" class="btn btn-sm btn-success">Paid</a>
                                    <a href="#" class="btn btn-sm btn-danger">Defaulted</a></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-contact1" role="tabpanel" aria-labelledby="nav-contact-tab">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($loans as $loan)
                        @if($loan->status == 'approved')
                            <tr>
                                <td>{{$loan->owner->name}}</td>
                                <td>{{$loan->amount.' '.$loan->currency}}}</td>
                                <td>{{strtoupper($loan->due_date)}}</td>
                                <td><a href="#" class="btn btn-sm btn-success">Approve</a>
                                    <a href="#" class="btn btn-sm btn-danger">Deny</a></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="nav-contact2" role="tabpanel" aria-labelledby="nav-contact-tab">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Denied By</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($loans as $loan)
                        @if($loan->status == 'denied')
                            <tr>
                                <td>{{$loan->owner->name}}</td>
                                <td>{{$loan->amount.' '.$loan->currency}}}</td>
                                <td>{{strtoupper($loan->due_date)}}</td>
                                <td>{{strtoupper($loan->handle_by)}}</td>
                                <td><a href="#" class="btn btn-sm btn-success">Paid</a>
                                    <a href="#" class="btn btn-sm btn-danger">Defaulted</a></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewClientModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4>Identification</h4>
                    <div class="row">
                        <img id="identification" />
                    <h4>Payslip</h4>
                    </div>
                    <div class="row">
                        <img id="payslip" />
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function loadImages(number){
            document.getElementById('identification').src='clients/'+number+'/id.jpg';
            document.getElementById('payslip').src='clients/'+number+'/payslip.jpg';
        }
    </script>
</div>
