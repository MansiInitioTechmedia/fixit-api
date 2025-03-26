<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;

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
            //$name = $user ? $user->full_name : "User";
        
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
            $validator = validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|string|min:4',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'data' => $validator->errors(),
                ], 200);
            }

            $email = $request->input('email');
            $otp = $request->input('otp');

            $resetData = DB::table('password_resets')->where('email', '=', $email)->first();

            if ($resetData || $resetData->otp == $otp || $resetData->expiry_time < now()) {
                return response()->json([
                    'status' => true,
                    'message' => 'OTP verified successfully. You can now reset your password.',
                    'data' => null,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired OTP.',
                    'data' => null,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while verifying the OTP.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }
}
