<?php

namespace App\Exceptions;

use App\Models\IncomingMessage;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Models\IncomingMessageLog;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $date = new DateTime();
        $formattedDate = $date->format('d-m-Y');

        // Initialize variables for logging
        $responseMessage = '';

        // Check if the exception is a ValidationException
        if ($exception instanceof ValidationException) {
            $responseMessage = json_encode($exception->errors());

            $log = IncomingMessageLog::create([
                'request_url' => $request->url(),
                'response_url' => $request->fullUrl(),
                'response_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'response_message' => $responseMessage,
            ]);

            IncomingMessage::create([
                'log_id' => $log->log_id,
                'tdate' => $formattedDate,
                'email' => $request->user()->email ?? '',
                'mobile_number' => $request->user()->phone ?? '',
                'profile_name' => $request->user()->name ?? '',
            ]);
        } else {
            // Handle non-validation exceptions
            $responseMessage = $exception->getMessage();

            $log = IncomingMessageLog::create([
                'request_url' => $request->url(),
                'response_url' => $request->fullUrl(),
                'response_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'response_message' => $responseMessage,
            ]);

            IncomingMessage::create([
                'log_id' => $log->log_id,
                'tdate' => $formattedDate,
                'email' => $request->user()->email ?? '',
                'mobile_number' => $request->user()->phone ?? '',
                'profile_name' => $request->user()->name ?? '',
            ]);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        try {
            $date = new DateTime();
            $formattedDate = $date->format('d-m-Y');

            $log = IncomingMessageLog::create([
                'request_url' => $request->url(),
                'response_code' => 401,
                'response_message' => 'Unauthenticated, kindly provide a valid token.',
                'response_url' => $request->fullUrl(),
            ]);

            IncomingMessage::create([
                'log_id' => $log->log_id,
                'tdate' => $formattedDate,
            ]);

            return response()->json(['message' => 'Unauthenticated, kindly provide a valid token.'], 401);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
