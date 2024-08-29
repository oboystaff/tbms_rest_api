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
use App\Action\Common\CommonTask;
use Illuminate\Http\Request;


class AccountController extends Controller
{
    public function index(Request $request, $login_id)
    {
        try {
            $data = Account::with(['incomingMessage'])
                ->orderBy('created_at', 'DESC')
                ->where('login_id', $login_id)
                ->get();

            if (count($data) == 0) {
                $msg = 'Account for the login id not found';
                LogError::createLogError($request, $msg);

                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => $msg
                ], 422);
            }

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

            $logModel = new IncomingMessageLog();
            $messageModel = new IncomingMessage();

            $data = $request->validated();
            $logData = CommonTask::pushData($data, $logModel);
            $data['log_id'] = $logData->log_id;
            $messageData = CommonTask::pushData($data, $messageModel);

            $incomingLog = IncomingMessageLog::where('log_id', $data['log_id'])->first();
            $incomingMessage = IncomingMessage::where('log_id', $data['log_id'])->first();

            if ($validateCheckSumNum > 0) {
                $account = Account::create($request->validated());

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Account created successfully',
                    'data' => $account
                ]);

                $incomingLog->update([
                    'request_url' => $request->url(),
                    'response_url' => $request->fullUrl(),
                    'response_code' => $response->status(),
                    'response_message' => $response->getData()->message,
                    'incoming_checksum' => $checksum,
                    'checksum' => $originalCheckSumNum,
                    'checksum_status' => 'success'
                ]);

                $incomingMessage->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => $originalCheckSumNum,
                    'checksum_status' => 'success'
                ]);

                return $response;
            } else {
                $incomingLog->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => $originalCheckSumNum,
                    'checksum_status' => 'failed'
                ]);

                $incomingMessage->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => $originalCheckSumNum,
                    'checksum_status' => 'failed'
                ]);

                return LogError::createLogError($request, 'Invalid CheckSum try again');
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function show(Request $request, $login_id, $account_number)
    {
        try {
            $account = Account::with(['incomingMessage'])
                ->where('login_id', $login_id)
                ->where('account_number', $account_number)
                ->first();

            if (empty($account)) {
                $msg = 'Account not found';
                LogError::createLogError($request, $msg);

                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => $msg
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
                $msg = 'Account not found';
                LogError::createLogError($request, $msg);

                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => $msg
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

            $logModel = new IncomingMessageLog();
            $messageModel = new IncomingMessage();

            $data = $request->validated();
            $logData = CommonTask::pushData($data, $logModel);
            $data['log_id'] = $logData->log_id;
            $messageData = CommonTask::pushData($data, $messageModel);

            $incomingLog = IncomingMessageLog::where('log_id', $data['log_id'])->first();
            $incomingMessage = IncomingMessage::where('log_id', $data['log_id'])->first();

            if ($validateCheckSumNum > 0) {
                $account->update($request->validated());

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Account updated successfully'
                ]);

                $incomingLog->update([
                    'request_url' => $request->url(),
                    'response_url' => $request->fullUrl(),
                    'response_code' => $response->status(),
                    'response_message' => $response->getData()->message,
                    'incoming_checksum' => $checksum,
                    'checksum' => $originalCheckSumNum,
                    'checksum_status' => 'success'
                ]);

                $incomingMessage->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => $originalCheckSumNum,
                    'checksum_status' => 'success'
                ]);

                return $response;
            } else {
                $incomingLog->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => $originalCheckSumNum,
                    'checksum_status' => 'failed'
                ]);

                $incomingMessage->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => $originalCheckSumNum,
                    'checksum_status' => 'failed'
                ]);

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
