<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\CreateRegistrationRequest;
use App\Http\Requests\Registration\UpdateRegistrationRequest;
use App\Models\Registration;
use App\Models\IncomingMessage;
use App\Models\IncomingMessageLog;
use App\Action\CheckSum\CheckSum;
use App\Action\Log\LogError;
use App\Action\Common\CommonTask;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index()
    {
        try {
            $data = Registration::with(['incomingMessage'])
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Get all Registrations',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function store(CreateRegistrationRequest $request)
    {
        try {
            $mobileNumber = $request->mobile_number;
            $email = $request->email;
            $trace_id = $request->trace_id;
            $echannel = $request->echannel;

            $stringContNum = $mobileNumber . $echannel . $trace_id;
            $stringContEmail = $email . $echannel . $trace_id;

            $checksum = $request->header('CheckSum');

            if (!$checksum) {
                return LogError::createLogError($request, 'CheckSum header is required');
            }

            $originalCheckSumNum = CheckSum::createChecksum($stringContNum, env('exchangeKey'));
            $originalCheckSumEmail = CheckSum::createChecksum($stringContEmail, env('exchangeKey'));

            $validateCheckSumNum = CheckSum::isChecksumValid($checksum, $originalCheckSumNum);
            $validateCheckSumEmail = CheckSum::isChecksumValid($checksum, $originalCheckSumEmail);

            $logModel = new IncomingMessageLog();
            $messageModel = new IncomingMessage();

            $data = $request->validated();
            $data['profile_name'] = $data['pname'];
            $data['mobile_number'] = $data['mno'];
            unset($data['pname']);
            unset($data['mno']);
            $logData = CommonTask::pushData($data, $logModel);
            $data['log_id'] = $logData->log_id;
            $messageData = CommonTask::pushData($data, $messageModel);

            $incomingLog = IncomingMessageLog::where('log_id', $data['log_id'])->first();
            $incomingMessage = IncomingMessage::where('log_id', $data['log_id'])->first();

            //Check if checkSum is valid
            if ($validateCheckSumNum > 0 || $validateCheckSumEmail > 0) {
                $registration = Registration::create($request->validated());

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Registration created successfully',
                    'data' => $registration
                ]);

                $incomingLog->update([
                    'request_url' => $request->url(),
                    'response_url' => $request->fullUrl(),
                    'response_code' => $response->status(),
                    'response_message' => $response->getData()->message,
                    'incoming_checksum' => $checksum,
                    'checksum' => ($validateCheckSumNum === 1) ? $originalCheckSumNum : $originalCheckSumEmail,
                    'checksum_status' => 'success'
                ]);

                $incomingMessage->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => ($validateCheckSumNum === 1) ? $originalCheckSumNum : $originalCheckSumEmail,
                    'checksum_status' => 'success'
                ]);

                return $response;
            } else {
                $incomingLog->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => ($validateCheckSumNum === 1) ? $originalCheckSumNum : $originalCheckSumEmail,
                    'checksum_status' => 'failed'
                ]);

                $incomingMessage->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => ($validateCheckSumNum === 1) ? $originalCheckSumNum : $originalCheckSumEmail,
                    'checksum_status' => 'failed'
                ]);

                return LogError::createLogError($request, 'Invalid CheckSum try again');
            }
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $registration = Registration::query()
                ->where('id', $id)
                ->orWhere('trace_id', $id)
                ->first();

            if (empty($registration)) {
                $msg = 'Registration not found';
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
                'message' => 'Get particular registration',
                'data' => $registration
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function update(UpdateRegistrationRequest $request, $id)
    {
        try {
            $registration = Registration::query()
                ->where('id', $id)
                ->orWhere('trace_id', $id)
                ->first();

            if (empty($registration)) {
                $msg = 'Registration not found';
                LogError::createLogError($request, $msg);

                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => $msg
                ], 422);
            }

            $mobileNumber = $request->mobile_number;
            $email = $request->email;
            $trace_id = $request->trace_id;
            $echannel = $request->echannel;

            $stringContNum = $mobileNumber . $echannel . $trace_id;
            $stringContEmail = $email . $echannel . $trace_id;

            $checksum = $request->header('CheckSum');

            if (!$checksum) {
                return LogError::createLogError($request, 'CheckSum header is required');
            }

            $originalCheckSumNum = CheckSum::createChecksum($stringContNum, env('exchangeKey'));
            $originalCheckSumEmail = CheckSum::createChecksum($stringContEmail, env('exchangeKey'));

            $validateCheckSumNum = CheckSum::isChecksumValid($checksum, $originalCheckSumNum);
            $validateCheckSumEmail = CheckSum::isChecksumValid($checksum, $originalCheckSumEmail);

            $logModel = new IncomingMessageLog();
            $messageModel = new IncomingMessage();

            $data = $request->validated();
            $data['profile_name'] = $data['pname'];
            $data['mobile_number'] = $data['mno'];
            unset($data['pname']);
            unset($data['mno']);
            $logData = CommonTask::pushData($data, $logModel);
            $data['log_id'] = $logData->log_id;
            $messageData = CommonTask::pushData($data, $messageModel);

            $incomingLog = IncomingMessageLog::where('log_id', $data['log_id'])->first();
            $incomingMessage = IncomingMessage::where('log_id', $data['log_id'])->first();

            //Check if checkSum is valid
            if ($validateCheckSumNum > 0 || $validateCheckSumEmail > 0) {
                $registration->update($request->validated());

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Registration updated successfully'
                ]);

                $incomingLog->update([
                    'request_url' => $request->url(),
                    'response_url' => $request->fullUrl(),
                    'response_code' => $response->status(),
                    'response_message' => $response->getData()->message,
                    'incoming_checksum' => $checksum,
                    'checksum' => ($validateCheckSumNum === 1) ? $originalCheckSumNum : $originalCheckSumEmail,
                    'checksum_status' => 'success'
                ]);

                $incomingMessage->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => ($validateCheckSumNum === 1) ? $originalCheckSumNum : $originalCheckSumEmail,
                    'checksum_status' => 'success'
                ]);

                return $response;
            } else {
                $incomingLog->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => ($validateCheckSumNum === 1) ? $originalCheckSumNum : $originalCheckSumEmail,
                    'checksum_status' => 'failed'
                ]);

                $incomingMessage->update([
                    'incoming_checksum' => $checksum,
                    'checksum' => ($validateCheckSumNum === 1) ? $originalCheckSumNum : $originalCheckSumEmail,
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
            $dailyRegistration = Registration::whereDate('created_at', date('Y-m-d'))
                ->count();

            $weeklyRegistration = Registration::whereBetween('created_at', [
                date('Y-m-d', strtotime('monday this week')),
                date('Y-m-d', strtotime('sunday this week'))
            ])
                ->count();

            $monthlyRegistration = Registration::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->count();

            $yearlyRegistration = Registration::whereYear('created_at', date('Y'))
                ->count();

            $data = [
                'daily_registration_total' => isset($dailyRegistration) ? $dailyRegistration : 0,
                'weekly_registration_total' => isset($weeklyRegistration) ? $weeklyRegistration : 0,
                'monthly_registration_total' => isset($monthlyRegistration) ? $monthlyRegistration : 0,
                'yearly_registration_total' => isset($yearlyRegistration) ? $yearlyRegistration : 0
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
