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

        if ($exception instanceof ValidationException) {
            $incomingMessage = IncomingMessage::create([
                'tdate' => $formattedDate,
                'email' => $request->user()->email ?? '',
                'mobile_number' => $request->user()->phone ?? '',
                'profile_name' => $request->user()->name ?? ''
            ]);

            IncomingMessageLog::create([
                'incoming_messages_id' => $incomingMessage->inc_messages_id,
                'request_url' => $request->url(),
                'response_url' => $request->fullUrl(),
                'response_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'response_message' => json_encode($exception->errors()),
            ]);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        $date = new DateTime();
        $formattedDate = $date->format('d-m-Y');

        $incomingMessage = IncomingMessage::create([
            'tdate' => $formattedDate
        ]);

        IncomingMessageLog::create([
            'request_url' => $request->url(),
            'response_code' => 401,
            'response_message' => 'Unauthenticated, kindly provide a valid token.',
            'incoming_messages_id' => $incomingMessage->inc_messages_id,
            'response_url' => $request->fullUrl(),
        ]);

        return response()->json(['message' => 'Unauthenticated, kindly provide a valid token.'], 401);
    }
}
