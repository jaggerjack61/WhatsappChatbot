<div>
    <div class="p-5">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link {{$tab==1?'active':''}}" wire:click="set(1)" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Approved Users</button>
                <button class="nav-link {{$tab==2?'active':''}}" wire:click="set(2)"  id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Pending Users</button>
                <button class="nav-link {{$tab==3?'active':''}}" wire:click="set(3)"  id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile1" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Denied Users</button>
                <button class="nav-link {{$tab==4?'active':''}}" wire:click="set(4)"  id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Approved Loans</button>
                <button class="nav-link {{$tab==5?'active':''}}" wire:click="set(5)"  id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact1" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Pending Loans</button>
                <button class="nav-link {{$tab==6?'active':''}}" wire:click="set(6)"  id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact2" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Denied Loans</button>
                <button class="nav-link {{$tab==7?'active':''}}" wire:click="set(7)"  id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact3" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Paid Loans</button>
                <button class="nav-link {{($tab==8)?'active':''}}" wire:click="set(8)"  id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact4" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Defaulted Loans</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade {{$tab==1?'show active':''}}" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <div class="m-2">
                    <input type="text" class="form-control" placeholder="Search" wire:model="search">
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>EC Number</th>
                            <th>Bank</th>
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
                                <td>{{strtoupper($client->EC)}}</td>
                                <td>{{strtoupper($client->bank.':'.$client->account_number)}}</td>
                                <td>{{strtoupper($client->status)}}</td>
                                <td>{{$client->handler->name}}</td>
                                <td><a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewClientModal" onclick="
                                    loadImages('{{$client->phone_no}}')">View</a>
                                    <a href="#" wire:click="deny('{{$client->id}}')" class="btn btn-sm btn-danger">Un-register</a></td>
                            </tr>

                            @endif
                        @endforeach
                        <tr>{{$clients->links()}}</tr>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade {{$tab==2?'show active':''}}"  id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="m-2">
                    <input type="text" class="form-control" placeholder="Search" wire:model="search">
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>EC Number</th>
                        <th>Bank</th>
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
                                <td>{{strtoupper($client->EC)}}</td>
                                <td>{{strtoupper($client->bank.':'.$client->account_number)}}</td>
                                <td>{{strtoupper($client->status)}}</td>
                                <td><a href="#" wire:click="register('{{$client->id}}')" class="btn btn-sm btn-success">Register</a>
                                    <a href="#" wire:click="deny('{{$client->id}}')" class="btn btn-sm btn-danger">Deny</a>
                                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewClientModal" onclick="
                                    loadImages('{{$client->phone_no}}')">View</a>

                                </td>
                            </tr>

                        @endif
                    @endforeach
                    <tr>{{$clients->links()}}</tr>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade {{$tab==3?'show active':''}}" id="nav-profile1" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="m-2">
                    <input type="text" class="form-control" placeholder="Search" wire:model="search">
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>EC Number</th>
                        <th>Bank</th>
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
                                <td>{{strtoupper($client->EC)}}</td>
                                <td>{{strtoupper($client->bank.':'.$client->account_number)}}</td>
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
                {{$clients->links()}}</td>
            </div>
            <div class="tab-pane fade {{$tab==4?'show active':''}}"  id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Loan Amount</th>
                        <th>Paid Amount</th>
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
                                <td>{{$loan->amount.' '.$loan->currency}}</td>
                                <td>
                                    @php
                                       $total=0;
                                    @endphp
                                    @for($i = 1; $i <= 1; $i++)
                                        @foreach($payments as $payment)
                                            @if($loan->id==$payment->loan_id)
                                                @php
                                                    $total+=$payment->amount;
                                                @endphp
                                            @endif
                                        @endforeach
                                    @endfor
                                    {{$total.' '.$loan->currency}}
                                </td>
                                <td>{{date('Y-m-d', strtotime("+".$loan->due_date." months", strtotime($loan->updated_at)))}}</td>
                                <td>{{strtoupper($loan->handler->name)}}</td>
                                <td><a href="#" wire:click.stop="setLoanId('{{$loan->id}}')" data-bs-toggle="modal" data-bs-target="#addPaymentModal" class="btn btn-sm btn-success">Pay</a>
                                    <a href="#" wire:click="defaultLoan('{{$loan->id}}')" class="btn btn-sm btn-danger">Default</a>
                                <a href="#" wire:click="completeLoan('{{$loan->id}}')" class="btn btn-sm btn-primary">Complete</a></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade {{$tab==5?'show active':''}}"  id="nav-contact1" role="tabpanel" aria-labelledby="nav-contact-tab">
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
                        @if($loan->status == 'pending')
                            <tr>
                                <td>{{$loan->owner->name}}</td>
                                <td>{{$loan->amount.' '.$loan->currency}}</td>
                                <td>{{$loan->due_date.' Months'}}</td>
                                <td><a href="#" class="btn btn-sm btn-success" wire:click="approveLoan('{{$loan->id}}')">Approve</a>
                                    <a href="#" class="btn btn-sm btn-danger" wire:click="denyLoan('{{$loan->id}}')">Deny</a></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade {{$tab==6?'show active':''}}"  id="nav-contact2" role="tabpanel" aria-labelledby="nav-contact-tab">
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
                                <td>{{$loan->amount.' '.$loan->currency}}</td>
                                <td>{{strtoupper($loan->due_date)}} Months</td>
                                <td>{{strtoupper($loan->handler->name)}}</td>
                                <td><a href="#" class="btn btn-sm btn-success" wire:click="approveLoan('{{$loan->id}}')">Approve</a>

                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade {{$tab==7?'show active':''}}"  id="nav-contact3" role="tabpanel" aria-labelledby="nav-contact-tab">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>




                    </tr>
                    </thead>
                    <tbody>
                    @foreach($loans as $loan)
                        @if($loan->status == 'paid')
                            <tr>
                                <td>{{$loan->owner->name}}</td>
                                <td>{{$loan->amount.' '.$loan->currency}}</td>


                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade {{($tab==8)?'show active':''}}"  id="nav-contact4" role="tabpanel" aria-labelledby="nav-contact-tab">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>




                    </tr>
                    </thead>
                    <tbody>
                    @foreach($loans as $loan)
                        @if($loan->status == 'defaulted')
                            <tr>
                                <td>{{$loan->owner->name}}</td>
                                <td>{{$loan->amount.' '.$loan->currency}}</td>



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

    <div wire:ignore class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">


                        <div class="form-group">
                            <label for="inputEmail">Amount</label>
                            <input type="text" wire:model.lazy="amount"  name="email" class="form-control" id="inputEmail" placeholder="Amount">
                        </div>
                        <div class="form-group">
                            <label for="inputEmail">Notes</label>
                            <input type="text" name="name" wire:model.lazy="notes" class="form-control" id="inputEmail" placeholder="Notes">
                        </div>



                        <button wire:click="payLoan" class="btn btn-primary m-1">Pay</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

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
