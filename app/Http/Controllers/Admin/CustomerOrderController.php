<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ScheduleMeeting;
use App\Models\User;
use App\Models\VerifyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerOrderController extends Controller
{
    // public function index(Request $request, $user_id = null, $order_id = null)
    // {
    //     try {

    //         $search = $request->search;

    //         // =============================
    //         // Get Users
    //         // =============================
    //         // $users = User::withCount('orders')
    //         //     ->when($search, function ($query) use ($search) {
    //         //         $query->where(function ($q) use ($search) {
    //         //             $q->where('first_name', 'like', "%{$search}%")
    //         //                 ->orWhere('last_name', 'like', "%{$search}%")
    //         //                 ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
    //         //         });
    //         //     })
    //         //     ->when(!$search, function ($query) {
    //         //         $query->orderBy('orders_count', 'desc');
    //         //     })
    //         //     ->get();

    //         $users = User::withCount([
    //             'orders', // total orders count (for sidebar)
    //             'orders as filtered_orders_count' => function ($query) use ($request) {
    //                 // Apply filters for filtered count
    //                 if ($request->filled('payment_status')) {
    //                     $query->where('payment_status', $request->payment_status);
    //                 }
    //                 if ($request->filled('from_date')) {
    //                     $query->whereDate('created_at', '>=', $request->from_date);
    //                 }
    //                 if ($request->filled('to_date')) {
    //                     $query->whereDate('created_at', '<=', $request->to_date);
    //                 }
    //             }
    //         ])
    //             ->when(
    //                 $request->filled('payment_status') || $request->filled('from_date') || $request->filled('to_date'),
    //                 function ($query) use ($request) {
    //                     // Filter users by filtered orders
    //                     $query->whereHas('orders', function ($q) use ($request) {
    //                         if ($request->filled('payment_status')) {
    //                             $q->where('payment_status', $request->payment_status);
    //                         }
    //                         if ($request->filled('from_date')) {
    //                             $q->whereDate('created_at', '>=', $request->from_date);
    //                         }
    //                         if ($request->filled('to_date')) {
    //                             $q->whereDate('created_at', '<=', $request->to_date);
    //                         }
    //                     });
    //                 }
    //             )
    //             ->when($search, function ($query) use ($search) {
    //                 $query->where(function ($q) use ($search) {
    //                     $q->where('first_name', 'like', "%{$search}%")
    //                         ->orWhere('last_name', 'like', "%{$search}%")
    //                         ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
    //                 });
    //             })
    //             ->orderByDesc('filtered_orders_count')
    //             ->get();

    //         // =============================
    //         // Select User
    //         // =============================
    //         $selectedUser = null;
    //         $orders = collect();
    //         $selectedOrder = null;

    //         if ($users->count() > 0) {

    //             if ($user_id) {
    //                 $selectedUser = $users->where('id', $user_id)->first();
    //             }

    //             // fallback to first user
    //             if (!$selectedUser) {
    //                 $selectedUser = $users->first();
    //             }

    //             // =============================
    //             // Get Orders (only if user exists)
    //             // =============================
    //             if ($selectedUser) {

    //                 $orders = $selectedUser->orders()
    //                     ->latest()
    //                     ->paginate(5);

    //                 if ($order_id) {
    //                     $selectedOrder = $selectedUser->orders()
    //                         ->where('id', $order_id)
    //                         ->first();
    //                 }

    //                 // fallback to latest order
    //                 if (!$selectedOrder) {
    //                     $selectedOrder = $selectedUser->orders()
    //                         ->latest()
    //                         ->first();
    //                 }
    //             }
    //         }

    //         return view('admin.customer.index', compact(
    //             'users',
    //             'selectedUser',
    //             'orders',
    //             'selectedOrder'
    //         ));
    //     } catch (\Exception $e) {

    //         Log::error('Customer Index Error: ' . $e->getMessage());

    //         return redirect()->back()->with('error', 'Something went wrong.');
    //     }
    // }

    public function index(Request $request, $user_id = null, $order_id = null)
    {
        try {
            $search = $request->search;

            // =============================
            // Get Users with filtered_orders_count
            // =============================
            $users = User::withCount([
                'orders', // total orders count for sidebar
                'orders as filtered_orders_count' => function ($query) use ($request) {
                    if ($request->filled('payment_status')) {
                        $query->where('payment_status', $request->payment_status);
                    }
                    if ($request->filled('from_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date);
                    }
                    if ($request->filled('to_date')) {
                        $query->whereDate('created_at', '<=', $request->to_date);
                    }
                    if ($request->filled('pending_step')) {
                        $step = $request->pending_step;

                        if ($step === 'veriff') {
                            $query->where(function ($q2) {
                                $q2->whereDoesntHave('veriffData')
                                    ->orWhereHas('veriffData', function ($q3) {
                                        $q3->whereNotIn('status', ['verified', 'approved']);
                                    });
                            });
                        } elseif ($step === 'documents' || $step === 'document_verification') {
                            $query->where(function ($q2) {
                                $q2->whereNull('upload_document_status')
                                    ->orWhere('upload_document_status', '!=', 'verified');
                            });
                        } elseif ($step === 'meeting') {
                            $query->where(function ($q2) {
                                $q2->whereDoesntHave('scheduleMeeting')
                                    ->orWhereHas('scheduleMeeting', function ($q3) {
                                        $q3->where('status', '!=', 'verified');
                                    });
                            });
                        }
                    }
                }
            ])
                ->when(
                    $request->filled('payment_status') || $request->filled('from_date') || $request->filled('to_date') || $request->filled('pending_step'),
                    function ($query) use ($request) {
                        $query->whereHas('orders', function ($q) use ($request) {
                            // Payment and date filters
                            if ($request->filled('payment_status')) {
                                $q->where('payment_status', $request->payment_status);
                            }
                            if ($request->filled('from_date')) {
                                $q->whereDate('created_at', '>=', $request->from_date);
                            }
                            if ($request->filled('to_date')) {
                                $q->whereDate('created_at', '<=', $request->to_date);
                            }

                            // Pending steps
                            if ($request->filled('pending_step')) {
                                $step = $request->pending_step;

                                if ($step === 'veriff') {
                                    $q->where(function ($q2) {
                                        $q2->whereDoesntHave('veriffData')
                                            ->orWhereHas('veriffData', function ($q3) {
                                                $q3->whereNotIn('status', ['verified', 'approved']);
                                            });
                                    });
                                } elseif ($step === 'documents' || $step === 'document_verification') {
                                    $q->where(function ($q2) {
                                        $q2->whereNull('upload_document_status')
                                            ->orWhere('upload_document_status', '!=', 'verified');
                                    });
                                } elseif ($step === 'meeting') {
                                    $q->where(function ($q2) {
                                        $q2->whereDoesntHave('scheduleMeeting') // no meeting yet
                                            ->orWhereHas('scheduleMeeting', function ($q3) {
                                                $q3->where('status', '!=', 'verified'); // meeting exists but not verified
                                            });
                                    });
                                }
                            }
                        });
                    }
                )
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                })
                ->orderByDesc('filtered_orders_count')
                ->get();

            // =============================
            // Selected User and Orders
            // =============================
            $selectedUser = $user_id ? $users->where('id', $user_id)->first() : $users->first();
            $orders = collect();
            $selectedOrder = null;

            if ($selectedUser) {
                $ordersQuery = $selectedUser->orders()->latest();

                // Apply filters for selected user's orders
                if ($request->filled('payment_status')) {
                    $ordersQuery->where('payment_status', $request->payment_status);
                }
                if ($request->filled('from_date') && $request->filled('to_date')) {
                    $ordersQuery->whereBetween('created_at', [$request->from_date, $request->to_date]);
                }

                // Pending steps filter
                if ($request->filled('pending_step')) {
                    $step = $request->pending_step;

                    if ($step === 'veriff') {
                        $ordersQuery->where(function ($q) {
                            $q->whereDoesntHave('veriffData')
                                ->orWhereHas('veriffData', function ($q2) {
                                    $q2->whereNotIn('status', ['verified', 'approved']);
                                });
                        });
                    } elseif ($step === 'documents' || $step === 'document_verification') {
                        $ordersQuery->where(function ($q) {
                            $q->whereNull('upload_document_status')
                                ->orWhere('upload_document_status', '!=', 'verified');
                        });
                    } elseif ($step === 'meeting') {
                        $ordersQuery->where(function ($q) {
                            $q->whereDoesntHave('scheduleMeeting') // no meeting yet
                                ->orWhereHas('scheduleMeeting', function ($q2) {
                                    $q2->where('status', '!=', 'verified'); 
                                });
                        });
                    }
                }

                $orders = $ordersQuery->paginate(5)->appends($request->query());

                // Selected order
                $selectedOrder = $order_id
                    ? $selectedUser->orders()->where('id', $order_id)->first()
                    : (clone $ordersQuery)->first();
            }

            return view('admin.customer.index', compact('users', 'selectedUser', 'orders', 'selectedOrder'));
        } catch (\Exception $e) {
            Log::error('Customer Index Error: ' . $e->getMessage());
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
