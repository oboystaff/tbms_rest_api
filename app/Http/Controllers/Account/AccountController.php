<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\CreateAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Models\Account;
use App\Models\IncomingMessage;
use App\Models\IncomingMessageLog;
use App\Action\CheckSum\CheckSum;
use App\Action\Log\LogError;

class AccountController extends Controller
{
    public function index()
    {
        try {
            $data = Account::with(['incomingMessage'])
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Get all accounts',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function store(CreateAccountRequest $request)
    {
        try {
            $login_id = $request->login_id;
            $account_number = $request->account_number;
            $trace_id = $request->trace_id;

            $stringCont = $login_id . $account_number . $trace_id;

            $checksum = $request->header('CheckSum');

            if (!$checksum) {
                return LogError::createLogError($request, 'CheckSum header is required');
            }

            $originalCheckSumNum = CheckSum::createChecksum($stringCont, env('exchangeKey'));

            $validateCheckSumNum = CheckSum::isChecksumValid($checksum, $originalCheckSumNum);

            if ($validateCheckSumNum > 0) {
                $account = Account::create($request->validated());

                $incomingMessage = IncomingMessage::create([
                    'tdate' => $request->tdate,
                    'login_id' => $request->login_id,
                    'bank_code' => $request->bank_code,
                    'account_number' => $request->account_number,
                    'action' => $request->action,
                    'echannel' => $request->echannel,
                    'trace_id' => $request->trace_id,
                    'txn_type' => $request->txn_type
                ]);

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Account created successfully',
                    'data' => $account
                ]);

                IncomingMessageLog::create([
                    'incoming_messages_id' => $incomingMessage->inc_messages_id,
                    'request_url' => $request->url(),
                    'response_url' => $request->fullUrl(),
                    'response_code' => $response->status(),
                    'response_message' => $response->getData()->message
                ]);

                return $response;
            } else {
                return LogError::createLogError($request, 'Invalid CheckSum try again');
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function show($id)
    {
        try {
            $account = Account::with(['incomingMessage'])
                ->where('id', $id)
                ->orWhere('account_number', $id)
                ->first();

            if (empty($account)) {
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'Account not found'
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Get particular account',
                'data' => $account
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function update(UpdateAccountRequest $request, $id)
    {
        try {
            $account = Account::query()
                ->where('id', $id)
                ->orWhere('account_number', $id)
                ->first();

            if (empty($account)) {
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'Account not found'
                ], 422);
            }

            $login_id = $request->login_id;
            $account_number = $request->account_number;
            $trace_id = $request->trace_id;

            $stringCont = $login_id . $account_number . $trace_id;

            $checksum = $request->header('CheckSum');

            if (!$checksum) {
                return LogError::createLogError($request, 'CheckSum header is required');
            }

            $originalCheckSumNum = CheckSum::createChecksum($stringCont, env('exchangeKey'));

            $validateCheckSumNum = CheckSum::isChecksumValid($checksum, $originalCheckSumNum);

            if ($validateCheckSumNum > 0) {
                $account->update($request->validated());

                $incomingMessage = IncomingMessage::create([
                    'tdate' => $request->tdate,
                    'login_id' => $request->login_id,
                    'bank_code' => $request->bank_code,
                    'account_number' => $request->account_number,
                    'action' => $request->action,
                    'echannel' => $request->echannel,
                    'trace_id' => $request->trace_id,
                    'txn_type' => $request->txn_type
                ]);

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Account updated successfully'
                ]);

                IncomingMessageLog::create([
                    'incoming_messages_id' => $incomingMessage->inc_messages_id,
                    'request_url' => $request->url(),
                    'response_url' => $request->fullUrl(),
                    'response_code' => $response->status(),
                    'response_message' => $response->getData()->message
                ]);

                return $response;
            } else {
                return LogError::createLogError($request, 'Invalid CheckSum try again');
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function dashboard()
    {
        try {
            $dailyAccount = Account::whereDate('created_at', date('Y-m-d'))
                ->count();

            $weeklyAccount = Account::whereBetween('created_at', [
                date('Y-m-d', strtotime('monday this week')),
                date('Y-m-d', strtotime('sunday this week'))
            ])
                ->count();

            $monthlyAccount = Account::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->count();

            $yearlyAccount = Account::whereYear('created_at', date('Y'))
                ->count();

            $data = [
                'daily_account_total' => isset($dailyAccount) ? $dailyAccount : 0,
                'weekly_account_total' => isset($weeklyAccount) ? $weeklyAccount : 0,
                'monthly_account_total' => isset($monthlyAccount) ? $monthlyAccount : 0,
                'yearly_account_total' => isset($yearlyAccount) ? $yearlyAccount : 0
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
