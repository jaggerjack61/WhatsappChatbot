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
}
