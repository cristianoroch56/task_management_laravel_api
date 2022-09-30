<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class UserController extends Controller
{
    //
    public function getUsers(Request $request)
    {
        try {
            $users = User::orderBy('id', 'desc')->get();

            return $this->sendResponse($users, 'User List.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Something went wrong!', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUserDetails(Request $request)
    {
        $users = User::whereId($request->id)->first();

        if (empty($users)) {
            return $this->sendError('User does not exists.', [], HttpResponse::HTTP_NOT_FOUND);
        }

        return $this->sendResponse($users, 'User found successfully.');
    }

    public function addUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'email' => 'required|email|regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/|unique:users,email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $key => $value) {
                    return $this->sendError($value[0], [], HttpResponse::HTTP_BAD_REQUEST);
                }
            }

            if (User::where('email', strtolower($request->email))->exists()) {
                return $this->sendError('Email already register. Please try again.', [], HttpResponse::HTTP_BAD_REQUEST);
            }

            if (!in_array($request->role, array_flip(User::USER_ROLE))) {
                return $this->sendError('Invalid user role request', [], HttpResponse::HTTP_BAD_REQUEST);
            }

            $user = new User();
            $user->username = $request->username;
            $user->email = strtolower($request->email);
            $user->password = Hash::make($request->password);
            $user->role = !empty($request->role) ? $request->role : User::USER_ROLE['User'];

            if (!$user->save()) {
                return $this->sendError('Something went wrong while creating the user.');
            }

            return $this->sendResponse([], 'User saved successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError('Something went wrong!', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateUser(Request $request, $userId = null)
    {
        try {
            $user = User::whereId($request->userId)->first();

            if (empty($user)) {
                return $this->sendError('User does not exists.', [], HttpResponse::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:users,username,' . $user->id,
                'email' => 'required|email|regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/|unique:users,email,' . $user->id
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $key => $value) {
                    return $this->sendError($value[0], [], 400);
                }
            }

            if (!in_array($request->role, array_flip(User::USER_ROLE))) {
                return $this->sendError('Invalid user role request', [], HttpResponse::HTTP_BAD_REQUEST);
            }

            if ($request->filled('username')) $user->username = $request->username;
            if ($request->filled('email')) $user->email = strtolower($request->email);
            $user->role = !empty($request->role) ? $request->role : User::USER_ROLE['User'];

            $user->save();

            return $this->sendResponse([], 'User Profile Updated Successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError('Something went wrong!', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteUser(Request $request, $userId = null)
    {
        try {
            $user = User::whereId($request->userId)->first();
            if (empty($user)) {
                return $this->sendError('User does not exists.', [], HttpResponse::HTTP_NOT_FOUND);
            }
            $user->delete();
            return $this->sendResponse([], 'User deleted Successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError('Something went wrong!', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
