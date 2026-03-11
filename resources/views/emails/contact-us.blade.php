@extends('emails.default')

@section('content')
<tr>
    <td style="padding:32px; font-family: Arial, Helvetica, sans-serif;">

        <!-- Heading -->
        <h2 style="margin:0 0 16px; color:#2f3446; font-size:20px; font-weight:600;">
            New Contact Form Message
        </h2>

        <!-- Greeting -->
        <p style="margin:0 0 12px; color:#444; font-size:14px; line-height:1.7;">
            Hello Admin,
        </p>

        <!-- Intro -->
        <p style="margin:0 0 16px; color:#444; font-size:14px; line-height:1.7;">
            A new message has been submitted through the <strong>Contact Us</strong> form.
            Below are the details:
        </p>

        <!-- Contact Details Box -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="margin:16px 0; background:#f8fafc; border-left:4px solid #3b82f6;">
            <tr>
                <td style="padding:14px 18px; font-size:14px; color:#444; line-height:1.7;">
                    <strong>Name:</strong> {{ $data['name'] }} <br>
                    <strong>Email:</strong> {{ $data['email'] }} <br>
                    <strong>Phone:</strong> {{ $data['phone'] ?? 'N/A' }} <br>
                    <strong>Subject:</strong> {{ $data['subject'] ?? 'N/A' }}
                </td>
            </tr>
        </table>

        <!-- Message Box -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="margin:16px 0; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px;">
            <tr>
                <td style="padding:16px; font-size:14px; color:#444; line-height:1.7;">
                    <strong>Message:</strong>
                    <p style="margin-top:8px;">
                        {{ $data['message'] }}
                    </p>
                </td>
            </tr>
        </table>


        <!-- Footer Text -->
        <p style="margin-top:24px; color:#444; font-size:14px; line-height:1.7;">
            Please review this message and respond if necessary.
        </p>

    </td>
</tr>
@endsection
