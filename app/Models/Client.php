<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    protected $guarded=[];

    public function handler():belongsTo
    {
        return $this->belongsTo(User::class,'handled_by');
    }

    use HasFactory;
}
