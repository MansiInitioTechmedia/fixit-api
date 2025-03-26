<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
</head>
<body>
     <!-- FixIt Logo -->
     <img src="{{ asset('images/logo.png') }}" alt="FixIt Logo" class="logo">

  
<h2>Password Reset Request</h2>
    <p>Hello {{ $email }},</p>
    <p>Your OTP for password reset is: <strong>{{ $otp }}</strong></p>
    <p>This OTP is valid for 10 minutes.</p>
    <p>If you did not request a password reset, please ignore this email.</p>
    <p>If you did not request a password reset, please ignore this email.</p>

</body>
</html>