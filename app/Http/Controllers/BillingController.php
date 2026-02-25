<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;


class BillingController extends Controller
{
    public function billingDetails()
    {
        $orders = Order::where('user_id', auth()->id())
            ->where('payment_status', 'completed')
            ->latest()
            ->paginate(10);

        return view('front.user.billing.index', compact('orders'));
    }

    public function downloadInvoice($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        if (!$order->invoice_file_path) {
            abort(404);
        }

        return response()->download(
            storage_path('app/public/' . $order->invoice_file_path),
            $order->invoice_file_name
        );
    }
}
