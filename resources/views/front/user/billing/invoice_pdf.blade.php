<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #2b2b2b;
            margin: 0;
        }

        .container {
            padding: 30px;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .header td {
            vertical-align: top;
        }

        .logo {
            max-height: 65px;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            text-align: right;
            color: #111;
        }

        .invoice-meta {
            text-align: right;
            margin-top: 5px;
            line-height: 1.6;
        }

        .company-details {
            margin-top: 15px;
            line-height: 1.6;
        }

        .section {
            margin-top: 30px;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
            text-transform: uppercase;
            color: #444;
        }

        table.details-table {
            width: 100%;
            margin-top: 5px;
        }

        table.details-table td {
            vertical-align: top;
            line-height: 1.6;
        }

        table.item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.item-table th {
            background: #f2f2f2;
            padding: 10px;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 12px;
        }

        table.item-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .totals-table {
            width: 40%;
            margin-top: 20px;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .totals-table .label {
            background: #f9f9f9;
            font-weight: bold;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
            background: #f2f2f2;
        }

        .footer {
            clear: both;
            margin-top: 60px;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        hr {
            border: 0;
            border-top: 1px solid #e5e5e5;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Header -->
        <table class="header">
            <tr>
                <td>
                    <img src="file://{{ public_path('front/img/logo/logo.png') }}" class="logo">
                    <div class="company-details">
                        <strong>White Horse Notary</strong><br>
                        Solicitors & Notary Public<br>
                        123 Legal Street<br>
                        London, United Kingdom<br>
                        Email: info@whitehorsenotary.co.uk<br>
                        Phone: +44 20 1234 5678
                    </div>
                </td>
                <td>
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">
                        <strong>Invoice No:</strong> {{ $order->invoice_number }}<br>
                        <strong>Invoice Date:</strong>
                        {{ $order->invoice_generated_at
                            ? $order->invoice_generated_at->format('d/m/Y')
                            : $order->created_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}<br>
                        <strong>Order ID:</strong> #{{ $order->id }}<br>
                        <strong>Status:</strong> Paid
                    </div>
                </td>
            </tr>
        </table>

        <hr>

        <!-- Bill To -->
        <div class="section">
            <div class="section-title">Bill To :</div>
            <table class="details-table">
                <tr>
                    <td>
                        {{ $user->first_name . ' ' . $user->last_name }}<br>
                        {{ $user->email }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Service Details -->
        <div class="section">
            <div class="section-title">Service Details</div>

            <table class="item-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th width="20%" class="text-right">Amount (GBP)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            {{ $order->notaryServiceType?->name ?? 'Notary Service' }}<br>
                            <small>Order Reference: #{{ $order->id }}</small>
                        </td>
                        <td class="text-right">
                            £{{ number_format($order->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Totals -->
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="text-right">£{{ number_format($order->amount, 2) }}</td>
                </tr>


                <tr class="grand-total">
                    <td>Total Paid</td>
                    <td class="text-right">£{{ number_format($order->amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Info -->
        <div class="section" style="clear: both;">
            <div class="section-title">Payment Information</div>
            <strong>Payment Method:</strong> Stripe<br>
            <strong>Payment Status:</strong> Paid in full
        </div>

        <!-- Footer -->
        <div class="footer">
            This invoice confirms that payment has been received in full.<br>
            White Horse Notary – Solicitors & Notary Public<br>
            Registered in England & Wales
        </div>

    </div>
</body>

</html>
