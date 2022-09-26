<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentsLedger extends Model
{
    protected $guarded=[];
    use HasFactory;

    public function loan():belongsTo
    {
        return $this->belongsTo(LoanHistory::class,'loan_id');
    }
}
