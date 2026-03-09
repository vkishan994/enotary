<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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


        // Chart Data (example last 7 days orders)
        $data['ordersChart'] = Order::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total');

        $data['customersChart'] = User::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total');

        return view('admin.dashboard', $data);
    }

    public function filter(Request $request)
    {
        $filter = $request->filter;
        $type = $request->type;

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

            default:
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
        }

        if ($type == 'orders') {

            $count = Order::whereBetween('created_at', [$start, $end])->count();

            $chart = Order::whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total');

            return response()->json([
                'type' => 'orders',
                'count' => $count,
                'chart' => $chart
            ]);
        }

        if ($type == 'customers') {

            $count = User::whereBetween('created_at', [$start, $end])->count();

            $chart = User::whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total');

            return response()->json([
                'type' => 'customers',
                'count' => $count,
                'chart' => $chart
            ]);
        }

        if ($type == 'transactions') {

            $total = Order::whereBetween('created_at', [$start, $end])
                ->where('payment_status', 'completed')
                ->sum('amount');

            $chart = Order::whereBetween('created_at', [$start, $end])
                ->where('payment_status', 'completed')
                ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
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
