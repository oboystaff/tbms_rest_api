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

class ApplicationController extends Controller
{
    public function index()
    {
        try {
            $data = Application::with(['incomingMessage'])
                ->orderBy('created_at', 'DESC')
                ->get();

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

            if ($validateCheckSumNum > 0) {
                $application = Application::create($request->validated());

                $incomingMessage = IncomingMessage::create([
                    'acct_no' => $request->acct_no,
                    'login_id' => $request->login_id,
                    'tdate' => $request->tdate,
                    'sec_code' => $request->sec_code,
                    'amt' => $request->amt,
                    'next_of_kin' => $request->next_of_kin,
                    'nok_contact' => $request->next_of_kin_contact,
                    'trace_id' => $request->trace_id,
                    'bank_code' => $request->bank_code,
                    'txn_type' => $request->txn_type,
                    'country_code' => $request->country_code,
                    'echannel' => $request->echannel,
                    'funding_source' => $request->fsource,
                    'app_module' => $request->app_module,
                    'mobile_network' => $request->mno,
                    'cost' => $request->cost,
                    'face_value' => $request->face_value,
                    'int_rate' => $request->int_rate,
                    'disc_rate' => $request->disc_rate,
                    'value_date' => $request->value_date,
                    'maturity_date' => $request->mat_date,
                    'inv_amt_type' => $request->inv_amt_type
                ]);

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Application created successfully',
                    'data' => $application
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
            $application = Application::with(['incomingMessage'])
                ->where('id', $id)
                ->orWhere('acct_no', $id)
                ->first();

            if (empty($application)) {
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'Application not found'
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
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'Application not found'
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

            if ($validateCheckSumNum > 0) {
                $application->update($request->validated());

                $incomingMessage = IncomingMessage::create([
                    'acct_no' => $request->acct_no,
                    'login_id' => $request->login_id,
                    'tdate' => $request->tdate,
                    'sec_code' => $request->sec_code,
                    'amt' => $request->amt,
                    'next_of_kin' => $request->next_of_kin,
                    'nok_contact' => $request->next_of_kin_contact,
                    'trace_id' => $request->trace_id,
                    'bank_code' => $request->bank_code,
                    'txn_type' => $request->txn_type,
                    'country_code' => $request->country_code,
                    'echannel' => $request->echannel,
                    'funding_source' => $request->fsource,
                    'app_module' => $request->app_module,
                    'mobile_network' => $request->mno,
                    'cost' => $request->cost,
                    'face_value' => $request->face_value,
                    'int_rate' => $request->int_rate,
                    'disc_rate' => $request->disc_rate,
                    'value_date' => $request->value_date,
                    'maturity_date' => $request->mat_date,
                    'inv_amt_type' => $request->inv_amt_type
                ]);

                $response = response()->json([
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Application updated successfully'
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
