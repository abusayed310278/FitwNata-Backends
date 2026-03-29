<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">
</head>
<body>
    <div style="margin-top: 50px;">
        <table cellpadding="0" cellspacing="0" style="font-family: Nunito, sans-serif; font-size: 15px; font-weight: 400; max-width: 600px; border: none; margin: 0 auto; border-radius: 6px; overflow: hidden; background-color: #fff; box-shadow: 0 0 3px rgba(60, 72, 88, 0.15);">
            <thead>
                <tr style="background-color: #2D3591; line-height: 68px; text-align: center; color: #fff; font-size: 24px; letter-spacing: 1px;">
                    <th scope="col">{{ env('APP_NAME') }}</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="padding: 20px 24px 0px; color: #4a4a4a;">
                        Hi {{ $data['name'] ?? 'User' }},
                    </td>
                </tr>

                <tr>
                    <td style="padding: 15px 24px; color: #4a4a4a; font-size:16px;">
                        🎉 Welcome to <strong>{{ env('APP_NAME') }}</strong>! We’re excited to have you on board.
                    </td>
                </tr>

                <tr>
                    <td style="padding: 15px 24px; color: #4a4a4a;">
                        You can now log in to your account and start exploring all the features we’ve built for you.
                    </td>
                </tr>


                <tr>
                    <td style="padding: 15px 24px; color: #4a4a4a;">
                        If you need any help getting started, our support team is just a message away.
                    </td>
                </tr>

                <tr>
                    <td style="padding: 15px 24px 30px; color: #4a4a4a;">
                        Cheers,<br>
                        <strong>{{ env('APP_NAME') }} Team</strong>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 16px 8px; background-color: #f8f9fc; text-align: center; color: #7f8c8d; font-size: 13px;">
                        © {{ now()->year }} {{ env('APP_NAME') }}. All rights reserved.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
