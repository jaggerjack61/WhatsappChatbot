<div>
    <div class="p-5">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Approved Users</button>
                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Pending Users</button>
                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Approved Loans</button>
                <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact1" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Pending Loans</button>
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
                                <td><a href="#" class="btn btn-sm btn-primary">View</a>
                                    <a href="#" class="btn btn-sm btn-danger">Disable</a></td>
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
                                <td><a href="#" class="btn btn-sm btn-success">Register</a>
                                    <a href="#" class="btn btn-sm btn-danger">Deny</a></td>
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
        </div>
    </div>
</div>
