<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::with(['user', 'document', 'notaryServiceType'])->latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', fn($row) => $row->user->name ?? 'N/A')
                ->addColumn('user_email', fn($row) => $row->user->email ?? 'N/A')
                ->addColumn('document', fn($row) => $row->document->name ?? 'N/A')
                ->addColumn('service_type', fn($row) => $row->notaryServiceType->name ?? 'N/A')
                ->addColumn('amount', fn($row) => '£' . number_format($row->amount, 2))
                ->addColumn('status', function ($row) {
                    $badgeClass = $row->payment_status === 'completed' ? 'success' : ($row->payment_status === 'pending' ? 'warning' : 'danger');
                    return '<span class="badge bg-' . $badgeClass . '">' . ucfirst($row->payment_status) . '</span>';
                })
                ->addColumn('date', fn($row) => $row->created_at->format('d M Y, H:i'))
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('admin.orders.index');
    }
}
