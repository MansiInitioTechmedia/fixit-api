<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;
use App\Models\PasswordReset;


class ForgotPasswordController extends Controller
{
    public function forgototPassword(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'data' => $validator->errors(),
                ], 422);
            }

            // Get email from request
            $email = $request->input('email');

            // Generate OTP and expiry time
            $otp = random_int(1000, 9999);
            $expiry_time = now()->addMinutes(10);

            // Update or insert OTP into password_resets table
            DB::table('password_resets')->updateOrInsert(
                ['email' => $email],
                ['otp' => $otp, 'expiry_time' => $expiry_time, 'created_at' => now()]
            );

            // Fetch user's name from users table
            $user = DB::table('users')->where('email', $email)->first();
            $name = $user ? $user->full_name : "User";
        
            // $mailable = new ForgotPasswordMail($otp, $email);
            // return $mailable->render();

            // Send OTP email
            Mail::to($email)->send(new ForgotPasswordMail($otp, $email));

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully to your email address.',
                
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while sending the OTP.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function verifyOtp(Request $request)
{
    try {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $validator->errors(),
            ], 422);
        }

        // Extract input
        $email = $request->input('email');
        $otp = $request->input('otp');

        // Fetch OTP record from password_resets table
        $resetData = DB::table('password_resets')->where('email', $email)->first();

        // Check if OTP exists
        if (!$resetData) {
            return response()->json([
                'status' => false,
                'message' => 'OTP not found for this email.',
                'data' => null,
            ], 400);
        }

        // Check if OTP is expired
        if ($resetData->expiry_time < now()) {
            return response()->json([
                'status' => false,
                'message' => 'OTP has expired. Please request a new one.',
                'data' => null,
            ], 400);
        }

        // Verify OTP
        if ($resetData->otp != $otp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP. Please check and try again.',
                'data' => null,
            ], 400);
        }

        // OTP is valid
        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully. You can now reset your password.',
            'data' => null,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while verifying the OTP.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function resetPassword(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => ['required','string','min:8', 'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[@$!%*?&#]/', ], 
        ],[
            'email.exists' => 'The provided email does not exist in our records.',
            'email.email' => 'The provided email address is invalid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation fails',
                'data' => $validator->errors(),
            ], 422);

        }

        // Check if the OTP is valid
        $reset = PasswordReset::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$reset) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP or Expired OTP.',
                'data' => null,
            ], 400);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User  not found.',
                'data' => null,
            ], 404);
        }

        // Update the user's password
        $user->password = bcrypt($request->password);
        $user->save(); // Save the updated user

        // Delete the OTP record
        $reset->delete();
        // $formattedUser = $this->userController->formatUser($user);

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.',
            'data' => $user,
        ], 200);
    }
}
