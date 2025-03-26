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
    public function getData(Request $request)
    {
        echo "hello";
    }

    public function signup(Request $request)
    {
        $validateUser = Validator::make($request->all(),[
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required','string','min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/', ],
            'country_code' => 'required',
            'phone_number' => 'required|unique:users,phone_number',
            'gender' => 'nullable|in:male,female',
            'profile_picture' => 'string',
            'pin' => 'required|digits:4',
        ]);
        
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'data' => [
                    'errors' => $validateUser->errors()->all(),
                ]
            ], 422); 
        }
       
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password
            'country_code' => $request->country_code,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'profile_picture' => asset('storage/uploads/' . $request->profile_picture), // Store as JSON
            'pin' => $request->pin,
        ]);
      
        // $formattedUser = $this->UserController->formatUser($user);

        // return $this->returnSuccessMessage("User registered successfully.", $user);
    
        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => [
                'user' => $user,
            ],
        ], 201);
    }



    public function login(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = DB::table('users')->where('email', $request->email)->first();

            // User data not match with records or User not
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found or Email address not match with our data.',
                    'data' => null,
                ], 404); 
            }

            // Check if the password is correct
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password is incorrect.',
                    'data' => null,
                ], 401); 
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $authUser = Auth::user();

                $expiresAt = now()->addMinutes(value: 30);
                $token = $authUser->createToken('auth_token', ['API Token'], $expiresAt)->plainTextToken;
                $token = explode('|', $token)[1];

                // $authUser->tokens()->where('name', 'auth_token')->update(['expires_at' => $expiresAt]);
                // $formattedUser = $this->userController->formatUser($user);
                return response()->json([
                    'status' => true,
                    'message' => 'Login successful.',
                    'data' => [
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                        'user' => $user, 
                        // 'expire_At' => $expiresAt,
                    ],
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $e->errors(),
            ], 422); // Unprocessable Entity status

        } catch (\Exception $e) {
            // Catch any other unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during login.',
                'data' => $e->getMessage(),
            ], 500); // Internal Server Error status
        }
    }

}
