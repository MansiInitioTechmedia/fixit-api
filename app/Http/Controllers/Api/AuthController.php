<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\API\UserController;



class AuthController extends Controller
{
    public function signup(Request $request)
    {
        // Merge default values explicitly as strings
        $request->merge([
            'full_name' => (string) $request->input('full_name', 'none'),
            'profile_picture' => (string) $request->input('profile_picture', 'none'),
            'pin' => $request->pin === "" ? null : $request->pin, // Ensure it's a valid 4-digit string
            'gender' => $request->input('gender', 'male'),
        ]);

        // Validate the request
        $validateUser = Validator::make($request->all(), [
            'full_name' => 'sometimes|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/',],
            'country_code' => 'required',
            'phone_number' => 'required|string|digits:10|unique:users,phone_number',
            'gender' => 'nullable|in:male,female',
            'profile_picture' => 'sometimes|string',
            'pin' => 'nullable|integer',

        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => collect($validateUser->errors()->all())->implode(' '),
                'data' => null,
            ], 422);
        }

        if (!is_null($request->pin) && (!ctype_digit($request->pin) || strlen($request->pin) !== 4)) {
            return response()->json([
                'status' => false,
                'message' => 'The pin must be exactly 4 digits.',
                'data' => null,
            ], 422);
        }

        // Create a new user
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country_code' => $request->country_code,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'profile_picture' => asset('storage/uploads/' . $request->profile_picture),
            'pin' => $request->pin,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }




    public function login(Request $request)
{
    try {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'fcm_token' => 'required',
        ]);

        // Fetch user as Eloquent model
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or Email address not match with our data.',
                'data' => null,
            ], 404);
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password is incorrect, Please try again.',
                'data' => null,
            ], 401);
        }

        // Login user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();

            // Save the new fcm_token
            $authUser->fcm_token = $request->fcm_token;
            $authUser->save();

            // Generate access token
            $expiresAt = now()->addMinutes(30);
            $token = $authUser->createToken('auth_token', ['API Token'], $expiresAt)->plainTextToken;
            $token = explode('|', $token)[1];

            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $authUser, // updated user with fcm_token
                ],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Authentication failed.',
            'data' => null,
        ], 401);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => collect($e->errors())->flatten()->implode(' '),
            'data' => null,
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'data' => null,
        ], 500);
    }
}



    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();


        return response()->json([
            'status' => 'true',
            'message' => 'Logout Successfully',
            'data' => $user,
        ], 200);
    }

}
