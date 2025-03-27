<!DOCTYPE html>
<html>
<head>
    <title>Signup OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #000;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .email-container {
            max-width: 600px;
            background-color: #ffffff;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
        .header img {
            max-height: 50px;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
            margin: 10px 0 0;
        }
        .content {
            font-size: 16px;
            color: #555;
            padding: 20px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #000;
            display: inline-block;
            padding: 25px 25px;
            border-radius: 5px;
            margin-top: 15px;
            align-items: center;
            justify-content: center;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="email-container">
    <!-- Header Section (Company Logo & Name) -->
    <div class="header">
    <img src="https://http://127.0.0.1:8000/storage/uploads/logo.png" alt="Your Brand Logo">

        <h1>FixIt</h1>
    </div>

    <!-- Content -->
    <div class="content">
        <p>Hello {{ $name }},</p>
        <p>You requested a password reset for your account. Here is your One-Time Password (OTP):</p>
        
        <!-- OTP Display (Big & Highlighted) -->
        <div class="otp-code">{{ $otp }}</div>

        <p>This OTP is valid for 5 minutes.</p>
        <p>If you did not request this, please ignore this email.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        Regards, <br>
        FixIt Team
    </div>
</div>

</body>
</html>
