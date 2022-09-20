<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanHistory extends Model
{
    protected $guarded=[];
    use HasFactory;

    public function owner():belongsTo
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function handler():belongsTo
    {
        return $this->belongsTo(User::class,'handled_by');
    }
}
