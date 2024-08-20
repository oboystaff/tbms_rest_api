<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesPrimaryUuid;

class IncomingMessageLog extends Model
{
    use HasFactory, UsesPrimaryUuid;

    protected $table = 'echannels_incoming_messages_log';

    protected $guarded = [
        'id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $routine) {
            $routine->log_id =  $routine->generateLogID();
        });
    }

    public function generateLogID()
    {
        $log_id = rand(10000000, 99999999);

        while (self::where('log_id', $log_id)->exists()) {
            $this->generateLogID();
        }

        return $log_id;
    }
}
