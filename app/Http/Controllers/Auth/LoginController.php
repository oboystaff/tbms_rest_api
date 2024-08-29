<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;


class LoginController extends Controller
{
    public function index(LoginRequest $request)
    {
        try {
            $result = filter_var($request->validated('username'), FILTER_VALIDATE_EMAIL);

            if (empty($result)) {
                if (!auth()->attempt([
                    'phone' => $request->validated('username'),
                    'password' => $request->validated('password')
                ])) {
                    throw ValidationException::withMessages([
                        'username' => 'Your provided credentials could not be verified.'
                    ]);
                }
            }

            if (!empty($result)) {
                if (!auth()->attempt([
                    'email' => $request->validated('username'),
                    'password' => $request->validated('password')
                ])) {
                    throw ValidationException::withMessages([
                        'username' => 'Your provided credentials could not be verified.'
                    ]);
                }
            }

            $user = User::where('phone', $request->validated('username'))
                ->orWhere('email', $request->validated('username'))
                ->first();

            session()->regenerate();

            $token = explode('|', $user->createToken('auth_token')->plainTextToken, 2)[1];

            $user->tokens()->where('id', $user->tokens()->latest()->first()->id)
                ->update(['expires_at' => Carbon::now()->addMinutes(env('TOKEN_EXPIRATION_TIME'))]);

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'User logged in successfully',
                'data' => $user,
                'meta' => [
                    'token' => $token,
                    'timeout' => env('TOKEN_EXPIRATION_TIME') * 60
                ],
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'failed',
                'status_code' => 422,
                'message' => 'Unable to login. ' . $ex->getMessage(),
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'User logged out successfully'
        ]);
    }
}
