<?php

namespace App\Http\Controllers\IncomingMessage;

use App\Http\Controllers\Controller;
use App\Models\IncomingMessage;

class IncomingMessageController extends Controller
{
    public function index()
    {
        try {
            $data = IncomingMessage::with(['account', 'registration', 'application'])
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Get all echannel incoming messages',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function show($id)
    {
        try {
            $incomingMessage = IncomingMessage::with(['account', 'registration', 'application'])
                ->where('id', $id)
                ->first();

            if (empty($incomingMessage)) {
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'Incoming message not found'
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Get particular incoming message',
                'data' => $incomingMessage
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function dashboard()
    {
        try {
            $dailyIncomingMessage = IncomingMessage::whereDate('created_at', date('Y-m-d'))
                ->count();

            $weeklyIncomingMessage = IncomingMessage::whereBetween('created_at', [
                date('Y-m-d', strtotime('monday this week')),
                date('Y-m-d', strtotime('sunday this week'))
            ])
                ->count();

            $monthlyIncomingMessage = IncomingMessage::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->count();

            $yearlyIncomingMessage = IncomingMessage::whereYear('created_at', date('Y'))
                ->count();

            $data = [
                'daily_incoming_message_total' => isset($dailyIncomingMessage) ? $dailyIncomingMessage : 0,
                'weekly_incoming_message_total' => isset($weeklyIncomingMessage) ? $weeklyIncomingMessage : 0,
                'monthly_incoming_message_total' => isset($monthlyIncomingMessage) ? $monthlyIncomingMessage : 0,
                'yearly_incoming_message_total' => isset($yearlyIncomingMessage) ? $yearlyIncomingMessage : 0
            ];

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Get all dashboard data',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
