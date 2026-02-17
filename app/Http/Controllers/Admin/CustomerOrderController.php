<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request, $user_id = null)
    {
        $users = User::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->get();

        // If no user selected → pick first user
        if (!$user_id && $users->count() > 0) {
            $selectedUser = $users->first();
        } else {
            $selectedUser = User::with('orders')->findOrFail($user_id);
        }

        return view('admin.customer.index', [
            'users' => $users,
            'selectedUser' => $selectedUser
        ]);
    }
}
