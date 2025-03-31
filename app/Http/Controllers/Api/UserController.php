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

        // Merge default values explicitly as strings
        $request->merge([
            'full_name' => (string) $request->input('full_name', $user->full_name),
            'profile_picture' => (string) $request->input('profile_picture', $user->profile_picture),
            'pin' => $request->pin === "" ? null : $request->pin,
            'gender' => $request->input('gender', $user->gender),
        ]);

        // Validate input
        $validator = Validator::make($request->all(), [
            'full_name'       => 'sometimes|string|max:255',
            'email'           => 'sometimes|email|unique:users,email,' . $user->id,
            'password'        => ['sometimes', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/'],
            'country_code'    => 'sometimes|required',
            'phone_number'    => 'sometimes|string|digits:10|unique:users,phone_number,' . $user->id,
            'gender'          => 'nullable|in:male,female',
            'profile_picture' => 'sometimes|string',
            'pin'             => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => collect($validator->errors()->all())->implode(' '),
                'data'    => null
            ], 422);
        }

        if (!is_null($request->pin) && (!ctype_digit($request->pin) || strlen($request->pin) !== 4)) {
            return response()->json([
                'status' => false,
                'message' => 'The pin must be exactly 4 digits.',
                'data' => null,
            ], 422);
        }

        try {
            $updateData = $request->only([
                'full_name', 'email', 'country_code', 'phone_number', 'gender', 'pin'
            ]);

            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->has('profile_picture')) {
                $updateData['profile_picture'] = asset('storage/uploads/' . $request->profile_picture);
            }

            // Update user profile
            $user->update($updateData);

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