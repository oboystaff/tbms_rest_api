<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesPrimaryUuid;

class IncomingMessage extends Model
{
    use HasFactory, UsesPrimaryUuid;

    protected $table = 'echannels_incoming_messages';

    protected $guarded = [
        'id',
    ];

    public function log()
    {
        return $this->belongsTo(IncomingMessageLog::class, 'inc_messages_id', 'incoming_messages_id');
    }

    public function account()
    {
        return $this->hasMany(Account::class, 'trace_id', 'trace_id');
    }

    public function registration()
    {
        return $this->hasMany(Registration::class, 'trace_id', 'trace_id');
    }

    public function application()
    {
        return $this->hasMany(Application::class, 'trace_id', 'trace_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $routine) {
            $routine->inc_messages_id =  $routine->generateIncMessagesID();
        });
    }

    public function generateIncMessagesID()
    {
        $inc_messages_id = rand(10000000, 99999999);

        while (self::where('inc_messages_id', $inc_messages_id)->exists()) {
            $this->generateIncMessagesID();
        }

        return $inc_messages_id;
    }
}
