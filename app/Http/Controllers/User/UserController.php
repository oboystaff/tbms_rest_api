<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = User::orderBy('name', 'ASC')
                ->get();

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Get all users',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function show($id)
    {
        try {
            $user = User::query()
                ->where('id', $id)
                ->orWhere('phone', $id)
                ->orWhere('email', $id)
                ->first();

            if (empty($user)) {
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'User not found'
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Get particular user',
                'data' => $user
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::query()
                ->where('id', $id)
                ->orWhere('phone', $id)
                ->orWhere('email', $id)
                ->first();

            if (empty($user)) {
                return response()->json([
                    'status' => 'failed',
                    'status_code' => 422,
                    'message' => 'User not found'
                ], 422);
            }

            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);

            $user->update($data);

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'User updated successfully'
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
