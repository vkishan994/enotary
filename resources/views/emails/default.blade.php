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
                    @yield('content')

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
