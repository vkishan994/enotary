@extends('emails.default')

@section('content')
<tr>
    <td style="padding:32px; font-family: Arial, Helvetica, sans-serif;">

        <!-- Heading -->
        <h2 style="margin:0 0 16px; color:#2f3446; font-size:20px; font-weight:600;">
            New Document Verification Request
        </h2>

        <!-- Greeting -->
        <p style="margin:0 0 12px; color:#444; font-size:14px; line-height:1.7;">
            Hello Admin,
        </p>

        <!-- Intro -->
        <p style="margin:0 0 16px; color:#444; font-size:14px; line-height:1.7;">
            A user has submitted documents for verification. Below are the details:
        </p>

        <!-- User Details Box -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="margin:16px 0; background:#f8fafc; border-left:4px solid #3b82f6;">
            <tr>
                <td style="padding:14px 18px; font-size:14px; color:#444; line-height:1.7;">
                    <strong>User Name:</strong> {{ $user->first_name ?? $user->name }} <br>
                    <strong>Email:</strong> {{ $user->email }} <br>
                    <strong>Order ID:</strong> #{{ $order->id }}
                </td>
            </tr>
        </table>

        <!-- Button -->
        <table cellpadding="0" cellspacing="0" style="margin-top:24px;">
            <tr>
                <td>
                    <a href="{{ $orderUrl }}"
                        style="
                            display:inline-block;
                            padding:12px 20px;
                            background-color:#2563eb;
                            color:#ffffff;
                            text-decoration:none;
                            border-radius:6px;
                            font-size:14px;
                            font-weight:600;
                        ">
                        View Order Details
                    </a>
                </td>
            </tr>
        </table>

        <!-- Footer Text -->
        <p style="margin-top:24px; color:#444; font-size:14px; line-height:1.7;">
            Please review and verify the submitted documents from the admin panel.
        </p>

        <!-- Fallback Link -->
        <p style="margin-top:12px; font-size:12px; color:#666;">
            If the button does not work, copy and paste this link into your browser:<br>
            <a href="{{ $orderUrl }}" style="color:#2563eb;">{{ $orderUrl }}</a>
        </p>

    </td>
</tr>
@endsection
