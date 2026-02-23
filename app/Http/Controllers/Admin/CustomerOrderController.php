<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ScheduleMeeting;
use App\Models\User;
use App\Models\VerifyDocument;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request, $user_id = null, $order_id = null)
    {
        try {

            $search = $request->search;

            // =============================
            // Get Users
            // =============================
            $users = User::withCount('orders')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                })
                ->when(!$search, function ($query) {
                    $query->orderBy('orders_count', 'desc');
                })
                ->get();

            // =============================
            // Select User
            // =============================
            $selectedUser = null;
            $orders = collect();
            $selectedOrder = null;

            if ($users->count() > 0) {

                if ($user_id) {
                    $selectedUser = $users->where('id', $user_id)->first();
                }

                // fallback to first user
                if (!$selectedUser) {
                    $selectedUser = $users->first();
                }

                // =============================
                // Get Orders (only if user exists)
                // =============================
                if ($selectedUser) {

                    $orders = $selectedUser->orders()
                        ->latest()
                        ->paginate(5);

                    if ($order_id) {
                        $selectedOrder = $selectedUser->orders()
                            ->where('id', $order_id)
                            ->first();
                    }

                    // fallback to latest order
                    if (!$selectedOrder) {
                        $selectedOrder = $selectedUser->orders()
                            ->latest()
                            ->first();
                    }
                }
            }

            return view('admin.customer.index', compact(
                'users',
                'selectedUser',
                'orders',
                'selectedOrder'
            ));
        } catch (\Exception $e) {

            \Log::error('Customer Index Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function search(Request $request)
    {
        $search = $request->search;

        $users = User::withCount('orders')
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            })
            ->get();

        return response()->json($users);
    }

    public function uploadedDocument(Request $request, $order_id)
    {
        $order = Order::with(['user', 'document', 'notaryServiceType'])->findOrFail($order_id);
        $uploadedDocuments = VerifyDocument::with('verify_document_items')->where('order_id', $order_id)->get();
        return view('admin.customer.uploaded-doc', compact('order', 'uploadedDocuments'));
    }

    public function scheduleMeeting($id)
    {
        $meeting = ScheduleMeeting::findOrFail($id);
        return view('admin.customer.schedule-meeting', compact('meeting'));
    }
}
