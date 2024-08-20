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

            //Check if checkSum is valid
            if ($validateCheckSumNum > 0 || $validateCheckSumEmail > 0) {
                $registration = Registration::create($request->validated());

                $incomingMessage = IncomingMessage::create([
                    'profile_name' => $request->pname,
                    'email' => $request->email,
                    'tdate' => $request->tdate,
                    'echannel' => $request->echannel,
                    'trace_id' => $request->trace_id,
                    'mobile_number' => $request->mobile_number,
                    'txn_type' => $request->txn_type,
                    'country_code' => $request->country_code,
                    'mobile_network' => $request->mno
                ]);

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Registration created successfully',
                    'data' => $registration
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
            $registration = Registration::with(['incomingMessage'])
                ->where('id', $id)
                ->first();

            if (empty($registration)) {
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'Registration not found'
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
                ->first();

            if (empty($registration)) {
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'Registration not found'
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

            //Check if checkSum is valid
            if ($validateCheckSumNum > 0 || $validateCheckSumEmail > 0) {
                $registration->update($request->validated());

                $incomingMessage = IncomingMessage::create([
                    'profile_name' => $request->pname,
                    'email' => $request->email,
                    'tdate' => $request->tdate,
                    'echannel' => $request->echannel,
                    'trace_id' => $request->trace_id,
                    'mobile_number' => $request->mobile_number,
                    'txn_type' => $request->txn_type,
                    'country_code' => $request->country_code,
                    'mobile_network' => $request->mno
                ]);

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Registration updated successfully'
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
