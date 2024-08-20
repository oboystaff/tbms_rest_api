<?php

namespace App\Action\Log;

use App\Models\IncomingMessage;
use App\Models\IncomingMessageLog;
use Illuminate\Http\Request;
use DateTime;

class LogError
{
    public static function createLogError(Request $request, $message)
    {
        $date = new DateTime();
        $formattedDate = $date->format('d-m-Y');

        $incomingMessage = IncomingMessage::create([
            'tdate' => $formattedDate
        ]);

        IncomingMessageLog::create([
            'request_url' => $request->url(),
            'response_code' => 422,
            'response_message' => $message,
            'incoming_messages_id' => $incomingMessage->inc_messages_id,
            'response_url' => $request->fullUrl(),
        ]);

        return response()->json([
            'status' => 'failed',
            'status_code' => 422,
            'message' => $message
        ], 422);
    }
}
