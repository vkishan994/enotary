<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request, $user_id = null, $order_id = null)
    {
        $users = User::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->get();

        if (!$user_id && $users->count() > 0) {
            $selectedUser = $users->first();
        } else {
            $selectedUser = User::findOrFail($user_id);
        }

        $orders = $selectedUser->orders()
            ->latest()
            ->paginate(5);

        if ($order_id) {
            $selectedOrder = $selectedUser->orders()
                ->where('id', $order_id)
                ->first();
        } else {
            $selectedOrder = $selectedUser->orders()
                ->latest()
                ->first();
        }

        return view('admin.customer.index', compact(
            'users',
            'selectedUser',
            'orders',
            'selectedOrder'
        ));
    }
}
