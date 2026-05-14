<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Admin;
use App\Mail\OrderConfirmationMail;
use App\Mail\NewOrderAdminMail;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function completeOrder(Order $order, $notify = false)
    {
        // If already completed and we don't need to notify, just return
        if ($order->payment_status === 'completed' && $order->invoice_file_path && !$notify) {
            return;
        }

        // If already completed and we DO need to notify, we check if we already sent them
        // For simplicity, we'll use invoice_generated_at as a proxy or just let it send
        // (The user specifically wants it from the controller)
        if ($order->payment_status === 'completed' && $order->invoice_file_path && $notify) {
            // We can add a check here if we want to avoid double-sending
            // But for now, we'll proceed to ensure the user gets their mail as requested
        }

        // Update order status
        $order->payment_status = 'completed';
        
        if (!$order->invoice_number) {
            $order->invoice_number = 'INV-' . date('Y') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        }

        // Generate PDF if not exists
        $fileName = $order->invoice_number . '.pdf';
        $filePath = 'invoices/' . $fileName;

        if (!Storage::disk('public')->exists($filePath)) {
            $order->load(['user', 'notaryServiceType', 'document']);
            
            $pdf = Pdf::loadView('front.user.billing.invoice_pdf', [
                'order' => $order,
                'user' => $order->user
            ]);

            Storage::disk('public')->put($filePath, $pdf->output());
        }

        $order->invoice_file_name = $fileName;
        $order->invoice_file_path = $filePath;
        $order->invoice_generated_at = now();
        $order->save();

        // Send Emails and Notifications only if requested
        if ($notify) {
            $this->sendNotifications($order);
        }
    }

    protected function sendNotifications(Order $order)
    {
        // Send email to user
        try {
            Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
        } catch (\Exception $e) {
            Log::error('Order Confirmation Email Error: ' . $e->getMessage());
        }

        // Send email and notification to admin
        $admins = Admin::all();
        foreach ($admins as $admin) {
            // Send Email
            try {
                Mail::to($admin->email)->send(new NewOrderAdminMail($order));
            } catch (\Exception $e) {
                Log::error('New Order Admin Email Error: ' . $e->getMessage());
            }

            // Send Database Notification
            try {
                $admin->notify(new SystemNotification([
                    'type'    => 'new_order',
                    'title'   => 'New Order Received',
                    'message' => 'A new order (#' . $order->id . ') has been placed by ' . $order->user->first_name . ' ' . $order->user->last_name,
                    'url'     => route('admin.orders.detail', $order->id),
                    'icon'    => 'shopping-cart',
                ]));
            } catch (\Exception $e) {
                Log::error('New Order Admin Notification Error: ' . $e->getMessage());
            }
        }
    }
}
