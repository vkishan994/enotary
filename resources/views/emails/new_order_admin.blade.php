@extends('emails.default')

@section('content')
<tr>
    <td style="padding:32px; font-family: Arial, Helvetica, sans-serif;">

        <!-- Heading -->
        <h2 style="margin:0 0 16px; color:#2f3446; font-size:20px; font-weight:600;">
            New Order Received
        </h2>

        <!-- Greeting -->
        <p style="margin:0 0 12px; color:#444; font-size:14px; line-height:1.7;">
            Hello Admin,
        </p>

        <!-- Intro -->
        <p style="margin:0 0 16px; color:#444; font-size:14px; line-height:1.7;">
            A new order has been placed on the platform. Below are the details:
        </p>

        <!-- Order Details Box -->
        <table width="100%" cellpadding="0" cellspacing="0"
            style="margin:16px 0; background:#f8fafc; border-left:4px solid #d4af37;">
            <tr>
                <td style="padding:14px 18px; font-size:14px; color:#444; line-height:1.7;">
                    <strong>Order ID:</strong> #{{ $order->id }} <br>
                    <strong>Customer:</strong> {{ $order->user->first_name }} {{ $order->user->last_name }} ({{ $order->user->email }}) <br>
                    <strong>Service Type:</strong> {{ $order->notaryServiceType->name ?? 'N/A' }} <br>
                    <strong>Document:</strong> {{ $order->document->name ?? 'N/A' }} <br>
                    <strong>Amount Paid:</strong> {{ strtoupper($order->currency ?? 'GBP') }} {{ number_format($order->amount, 2) }} <br>
                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i A') }}
                </td>
            </tr>
        </table>

        <div style="margin-top: 25px; text-align: center;">
            <a href="{{ route('admin.orders.detail', $order->id) }}" 
               style="background-color: #2f3446; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
               View Order Details
            </a>
        </div>

    </td>
</tr>
@endsection
