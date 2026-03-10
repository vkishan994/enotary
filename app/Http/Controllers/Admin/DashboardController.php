<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ScheduleMeeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->filter ?? 'today';

        switch ($filter) {

            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                break;

            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;

            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek();
                $end = Carbon::now()->subWeek()->endOfWeek();
                break;

            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;

            default: // today
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
        }


        // Total Numbers
        $data['ordersCount'] = Order::whereBetween('created_at', [$start, $end])->count();

        $data['customersCount'] = User::whereBetween('created_at', [$start, $end])->count();

        $data['transactionsTotal'] = Order::whereBetween('created_at', [$start, $end])->where('payment_status', 'completed')->sum('amount');

        $data['meetingsCount'] = ScheduleMeeting::whereDate('meeting_date', Carbon::today())->count();

        $data['pendingDocumentOrders'] = Order::where('upload_document_status', '!=', 'verified')->count();

        $data['pendingDocumentVerification'] = Order::whereHas('verifyDocuments')
            ->whereHas('verifyDocuments', function ($q) {
                $q->where('status', '=', 'pending');
            })
            ->count();

        $data['pendingUploadDocuments'] = Order::whereDoesntHave('verifyDocuments')->count();

        $data['ordersWithUnverifiedDocs'] = Order::whereHas('verifyDocuments')
            ->whereHas('verifyDocuments', function ($q) {
                $q->where('status', '=', 'verified');
            })
            ->count();

        return view('admin.dashboard', $data);
    }

    public function filter(Request $request)
    {
        $filter = $request->filter;
        $type = $request->type;

        $start = null;
        $end = null;

        switch ($filter) {

            case 'all':
                // no date filter
                break;

            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                break;

            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;

            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek();
                $end = Carbon::now()->subWeek()->endOfWeek();
                break;

            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;

            default:
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
        }

        // ORDERS
        if ($type == 'orders') {

            $query = Order::query();

            if ($filter != 'all') {
                $query->whereBetween('created_at', [$start, $end]);
            }

            $count = $query->count();

            $chart = $query->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total');

            return response()->json([
                'type' => 'orders',
                'count' => $count,
                'chart' => $chart
            ]);
        }

        // CUSTOMERS
        if ($type == 'customers') {

            $query = User::query();

            if ($filter != 'all') {
                $query->whereBetween('created_at', [$start, $end]);
            }

            $count = $query->count();

            $chart = $query->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total');

            return response()->json([
                'type' => 'customers',
                'count' => $count,
                'chart' => $chart
            ]);
        }

        // TRANSACTIONS
        if ($type == 'transactions') {

            $query = Order::where('payment_status', 'completed');

            if ($filter != 'all') {
                $query->whereBetween('created_at', [$start, $end]);
            }

            $total = $query->sum('amount');

            $chart = $query->selectRaw('DATE(created_at) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total');

            return response()->json([
                'type' => 'transactions',
                'total' => $total,
                'chart' => $chart
            ]);
        }
    }
}
