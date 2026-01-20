<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Two-Factor Authentication</title>
</head>
<body style="margin:0; padding:0; background:#f2f2f2; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:40px 0;">
                <table width="600" cellpadding="0" cellspacing="0"
                       style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#2f3446; padding:22px; text-align:center;">
                            <h1 style="color:#ffffff; margin:0; font-size:22px; font-weight:600;">
                                {{ config('app.name') }}
                            </h1>
                            <p style="margin:5px 0 0; font-size:12px; color:#d4af37;">
                                Solicitors & Notary Public
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px;">
                            <h2 style="margin-top:0; color:#2f3446; font-size:20px;">
                                Reset Two-Factor Authentication
                            </h2>

                            <p style="color:#444; font-size:14px; line-height:1.7;">
                                We received a request to reset the two-factor authentication (2FA)
                                for your account.
                            </p>

                            <p style="color:#444; font-size:14px; line-height:1.7;">
                                If you no longer have access to your authenticator app,
                                you can reset your 2FA and set it up again securely by clicking the button below.
                            </p>

                            <!-- Button -->
                            <p style="text-align:center; margin:32px 0;">
                                <a href="{{ route($context . '.2fa.reset', ['token' => $token]) }}"
                                   style="background:#b8860b; color:#ffffff;
                                          padding:14px 28px;
                                          text-decoration:none;
                                          font-weight:600;
                                          border-radius:6px;
                                          display:inline-block;
                                          font-size:14px;">
                                    Reset Two-Factor Authentication
                                </a>
                            </p>

                            <p style="color:#444; font-size:14px; line-height:1.6;">
                                This link will expire in <strong>10 minutes</strong> for security reasons.
                            </p>

                            <p style="color:#777; font-size:13px; line-height:1.6;">
                                If you did not request this reset, no action is required.
                                Your account remains secure.
                            </p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:28px 0;">

                            <p style="color:#999; font-size:12px;">
                                If the button above does not work, copy and paste this link into your browser:
                            </p>

                            <p style="word-break:break-all; font-size:12px; color:#555;">
                                {{ route($context . '.2fa.reset', ['token' => $token]) }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f6f6f6; padding:18px; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#777;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
