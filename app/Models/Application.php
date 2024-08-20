<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesPrimaryUuid;

class Application extends Model
{
    use HasFactory, UsesPrimaryUuid;

    protected $table = 'tBillApplication';

    protected $guarded = [
        'id',
    ];

    public function incomingMessage()
    {
        return $this->hasMany(IncomingMessage::class, 'trace_id', 'trace_id');
    }
}
