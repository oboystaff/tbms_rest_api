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

        $log = IncomingMessageLog::create([
            'request_url' => $request->url(),
            'response_code' => 422,
            'response_message' => $message,
            'response_url' => $request->fullUrl()
        ]);

        IncomingMessage::create([
            'log_id' => $log->log_id,
            'tdate' => $formattedDate
        ]);

        return response()->json([
            'status' => 'failed',
            'status_code' => 422,
            'message' => $message
        ], 422);
    }
}
