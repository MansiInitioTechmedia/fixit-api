<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    // Update user profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Modify validation rules to allow existing email and phone number
        $rules = [
            'full_name'       => 'nullable|string|max:255',
            'email'           => ($request->email !== $user->email) ? 'nullable|email|unique:users,email' : 'nullable|email',
            'country_code'    => 'nullable|string',
            'phone_number'    => ($request->phone_number !== $user->phone_number) ? 'nullable|string|digits:10|unique:users,phone_number' : 'nullable|string|digits:10',
            'gender'          => 'nullable|in:male,female',
            'profile_picture' => 'nullable|string',
            'pin'             => 'nullable|digits:4',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => collect($validator->errors()->all())->implode(' '),
                'data'    => null
            ], 422);
        }

        try {
            $updateData = [];

            // Only update fields that are provided in the request
            foreach ($request->all() as $key => $value) {
                if (!is_null($value) && in_array($key, ['full_name', 'email', 'country_code', 'phone_number', 'gender', 'pin'])) {
                    $updateData[$key] = $value;
                }
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->filled('profile_picture')) {
                $updateData['profile_picture'] = asset('storage/uploads/' . $request->profile_picture);
            }

            // Update user profile only if data is provided
            if (!empty($updateData)) {
                $user->update($updateData);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Profile updated successfully',
                'data'    => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
                'data'    => null
            ], 500);
        }
    }
}
