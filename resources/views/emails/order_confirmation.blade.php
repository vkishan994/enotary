@extends('emails.default')

@section('content')
<tr>
    <td style="padding:32px; font-family: Arial, Helvetica, sans-serif;">

        <!-- Heading -->
        <h2 style="margin:0 0 16px; color:#2f3446; font-size:20px; font-weight:600;">
            Order Confirmation
        </h2>

        <!-- Greeting -->
        <p style="margin:0 0 12px; color:#444; font-size:14px; line-height:1.7;">
            Hello {{ $order->user->first_name }} {{ $order->user->last_name }},
        </p>

        <!-- Intro -->
        <p style="margin:0 0 16px; color:#444; font-size:14px; line-height:1.7;">
            Thank you for your order! Your payment has been successfully processed.
            We have attached the invoice for your records.
        </p>

        <!-- Order Details Box -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="margin:16px 0; background:#f8fafc; border-left:4px solid #3b82f6;">
            <tr>
                <td style="padding:14px 18px; font-size:14px; color:#444; line-height:1.7;">
                    <strong>Order ID:</strong> #{{ $order->id }} <br>
                    <strong>Invoice Number:</strong> {{ $order->invoice_number }} <br>
                    <strong>Amount Paid:</strong> {{ strtoupper($order->currency ?? 'GBP') }} {{ number_format($order->amount, 2) }} <br>
                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}
                </td>
            </tr>
        </table>

        <!-- Next Steps -->
        <p style="margin-top:24px; color:#444; font-size:14px; line-height:1.7;">
            You can now proceed to upload your documents or schedule a meeting from your dashboard.
        </p>

        <div style="margin-top: 25px; text-align: center;">
            <a href="{{ route('user.account-dashboard') }}" 
               style="background-color: #2f3446; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
               Go to Dashboard
            </a>
        </div>

    </td>
</tr>
@endsection
