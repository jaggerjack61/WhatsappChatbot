<?php

namespace App\Http\Livewire;

use App\Http\Controllers\WhatsappMessagingController;
use App\Models\Client;
use App\Models\LoanHistory;
use App\Models\PaymentsLedger;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Dashboard extends Component
{
    use LivewireAlert;

    public $amount;
    public $loanId;
    public $notes;
    public $renderState=1;
    public $tab;

    public function mount(){
        $this->tab=1;
    }

    public function set($data)
    {
        $this->tab=$data;
    }

    public function render()
    {
//        if($this->renderState==0) {
//            $this->renderState = 1;
//            die();
//        }
        $clients=Client::all();
        $loans=LoanHistory::all();
        $payments=PaymentsLedger::all();
        return view('livewire.dashboard',compact('clients','loans','payments'));
    }

    public function register($id)
    {
        $client=Client::find($id);
        $client->update([
            'status' => 'registered',
            'handled_by'=>auth()->user()->id]);

        $client->save();




        $this->alert('success','User has been successfully registered');
        $msg=new WhatsappMessagingController();
        $msg->sendMsgText(
            $client->phone_no,
            'Your registration has been approved type hello to begin'
        );
    }

    public function deny($id)
    {
        $msg=new WhatsappMessagingController();
        $client=Client::find($id);
        $client->update([
            'status' => 'denied',
            'handled_by'=>auth()->user()->id]);
        $client->save();

        $this->alert('error','User has been been denied registration');
        $msg->sendMsgText(
            $client->phone_no,
            'Your registration has been denied. If you believe there has been a mistake contact us on.'
        );

    }

    public function approveLoan($id)
    {
        $msg=new WhatsappMessagingController();
        $loan=LoanHistory::find($id);
        $loan->update([
            'status' => 'approved',
            'handled_by'=>auth()->user()->id]);
        $loan->save();

        $this->alert('success','User has been approved for the loan.');
        $msg->sendMsgText(
            $loan->owner->phone_no,
            'Your loan application has been approved.'
        );
    }

    public function denyLoan($id)
    {
        $msg=new WhatsappMessagingController();
        $loan=LoanHistory::find($id);
        $loan->update([
            'status' => 'denied',
            'handled_by'=>auth()->user()->id]);
        $loan->save();

        $this->alert('error','User has been denied loan.');
        $msg->sendMsgText(
            $loan->owner->phone_no,
            'Your loan application has been denied. If you believe there has been a mistake contact us on.'
        );
    }

    public function setLoanId($loanId)
    {
        $this->loanId = $loanId;
//        $this->renderState=0;
    }

    public function payLoan()
    {
        $ledger=PaymentsLedger::create([
            'loan_id' => $this->loanId,
            'amount' => $this->amount,
            'notes'=>$this->notes
        ]);
        $ledger->save();
        $this->alert('success','You have successfully paid '.$ledger->amount);

    }

    public function defaultLoan($id)
    {
        $loan=LoanHistory::find($id);
        $loan->status = 'defaulted';
        $loan->save();
    }
}
