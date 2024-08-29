<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\CreateApplicationRequest;
use App\Http\Requests\Application\UpdateApplicationRequest;
use App\Models\Application;
use App\Models\IncomingMessage;
use App\Models\IncomingMessageLog;
use App\Action\CheckSum\CheckSum;
use App\Action\Log\LogError;
use App\Action\Common\CommonTask;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request, $login_id)
    {
        try {
            $data = Application::with(['incomingMessage'])
                ->orderBy('created_at', 'DESC')
                ->where('login_id', $login_id)
                ->get();

            if (count($data) == 0) {
                $msg = 'Application not found';
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
                'message' => 'Get all applications',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function store(CreateApplicationRequest $request)
    {
        try {
            $login_id = $request->login_id;
            $account_number = $request->acct_no;
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
            $data['nok_contact'] = $data['next_of_kin_contact'];
            $data['funding_source'] = $data['fsource'];
            $data['maturity_date'] = $data['mat_date'];
            unset($data['next_of_kin_contact']);
            unset($data['fsource']);
            unset($data['mat_date']);
            $logData = CommonTask::pushData($data, $logModel);
            $data['log_id'] = $logData->log_id;
            $messageData = CommonTask::pushData($data, $messageModel);

            $incomingLog = IncomingMessageLog::where('log_id', $data['log_id'])->first();
            $incomingMessage = IncomingMessage::where('log_id', $data['log_id'])->first();

            if ($validateCheckSumNum > 0) {
                $application = Application::create($request->validated());

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Application created successfully',
                    'data' => $application
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

    public function show(Request $request, $login_id, $acct_no, $trace_id)
    {
        try {
            $application = Application::with(['incomingMessage'])
                ->where('login_id', $login_id)
                ->where('acct_no', $acct_no)
                ->where('trace_id', $trace_id)
                ->first();

            if (empty($application)) {
                $msg = 'Application not found';
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
                'message' => 'Get particular application',
                'data' => $application
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function update(UpdateApplicationRequest $request, $id)
    {
        try {
            $application = Application::query()
                ->where('id', $id)
                ->orWhere('acct_no', $id)
                ->first();

            if (empty($application)) {
                $msg = 'Application not found';
                LogError::createLogError($request, $msg);

                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => $msg
                ], 422);
            }

            $login_id = $request->login_id;
            $account_number = $request->acct_no;
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
            $data['nok_contact'] = $data['next_of_kin_contact'];
            $data['funding_source'] = $data['fsource'];
            $data['maturity_date'] = $data['mat_date'];
            unset($data['next_of_kin_contact']);
            unset($data['fsource']);
            unset($data['mat_date']);
            $logData = CommonTask::pushData($data, $logModel);
            $data['log_id'] = $logData->log_id;
            $messageData = CommonTask::pushData($data, $messageModel);

            $incomingLog = IncomingMessageLog::where('log_id', $data['log_id'])->first();
            $incomingMessage = IncomingMessage::where('log_id', $data['log_id'])->first();

            if ($validateCheckSumNum > 0) {
                $application->update($request->validated());

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Application updated successfully'
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
                return LogError::createLogError($request, 'Invalid CheckSum try again');
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function dashboard()
    {
        try {
            $dailyApplication = Application::whereDate('created_at', date('Y-m-d'))
                ->count();

            $weeklyApplication = Application::whereBetween('created_at', [
                date('Y-m-d', strtotime('monday this week')),
                date('Y-m-d', strtotime('sunday this week'))
            ])
                ->count();

            $monthlyApplication = Application::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->count();

            $yearlyApplication = Application::whereYear('created_at', date('Y'))
                ->count();

            $data = [
                'daily_application_total' => isset($dailyApplication) ? $dailyApplication : 0,
                'weekly_application_total' => isset($weeklyApplication) ? $weeklyApplication : 0,
                'monthly_application_total' => isset($monthlyApplication) ? $monthlyApplication : 0,
                'yearly_application_total' => isset($yearlyApplication) ? $yearlyApplication : 0
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
