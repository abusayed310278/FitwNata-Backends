<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Contact Message - FitwNata</title>
</head>

<body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:white; border-radius:8px; padding:25px; box-shadow:0 2px 10px rgba(0,0,0,0.06);">
        <tr>
            <td>

                <!-- Header -->
                <h2 style="color:#11c6a5; margin-top:0;">New Support Request</h2>

                <p style="font-size:16px; color:#333;">
                    Dear Admin,<br><br>
                    You have received a new support message on <strong>FitwNata</strong>.
                </p>

                <!-- Details Table -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                    <tr>
                        <td style="padding:8px 0; color:#555; width:120px;"><strong>Name:</strong></td>
                        <td style="padding:8px 0; color:#333;">{{ $data['name'] }}</td>
                    </tr>

                    <tr>
                        <td style="padding:8px 0; color:#555;"><strong>Email:</strong></td>
                        <td style="padding:8px 0; color:#333;">{{ $data['email'] }}</td>
                    </tr>

                    <tr>
                        <td style="padding:8px 0; color:#555;"><strong>Subject:</strong></td>
                        <td style="padding:8px 0; color:#333;">{{ $data['subject'] }}</td>
                    </tr>

                    <tr>
                        <td style="padding:8px 0; vertical-align:top; color:#555;"><strong>Message:</strong></td>
                        <td style="padding:8px 0; color:#333;">
                            <div style="white-space:pre-line;">
                                {{ $data['message'] }}
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Footer -->
                <!-- <p style="margin-top:25px; font-size:14px; color:#777;">
                    This email was generated automatically by the FitwNata Support System.
                </p> -->

            </td>
        </tr>
    </table>

</body>
</html>
