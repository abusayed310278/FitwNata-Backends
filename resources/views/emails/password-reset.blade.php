<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            padding: 20px;
            text-align: center;
        }
        .header img {
            width: 150px;
            width: auto;
        }
        .content {
            padding: 30px;
        }
        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background: #006C6E;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.3s;
        }
        .button:hover {
            background: #006C6E;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #777;
            border-top: 1px solid #eee;
        }
        .footer a {
            color: #1a73e8;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .content {
                padding: 20px;
            }
            h2 {
                font-size: 20px;
            }
            .button {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo">
        </div>
        <div class="content">
            <h2>Hello {{ $user->name ?? 'User' }},</h2>
            <p>We received a request to reset your password. Click the button below to set a new password for your account.</p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" class="button">Reset Your Password</a>
            </div>
            <p>If you didn’t request a password reset, please ignore this email or contact our support team.</p>
        </div>
        <div class="footer">
            <p>Regards,<br>Our Team</p>
        </div>
    </div>
</body>
</html>
