<?php

namespace App\Http\Livewire;

use App\Models\Client;
use App\Models\LoanHistory;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Dashboard extends Component
{
    use LivewireAlert;

    public function render()
    {
        $clients=Client::all();
        $loans=LoanHistory::all();
        return view('livewire.dashboard',compact('clients','loans'));
    }

    public function register($id)
    {
        $client=Client::find($id);
        $client->update([
            'status' => 'registered',
            'handled_by'=>auth()->user()->id]);

        $client->save();

        $this->alert('success','User has been successfully registered');
    }

    public function deny($id)
    {
        $client=Client::find($id);
        $client->update([
            'status' => 'denied',
            'handled_by'=>auth()->user()->id]);
        $client->save();
        $this->alert('error','User has been been denied registration');
    }
}
