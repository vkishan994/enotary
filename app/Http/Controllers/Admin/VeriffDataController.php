<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VeriffData;
use Illuminate\Http\Request;

class VeriffDataController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = VeriffData::latest()->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return $row->user->first_name . ' ' . $row->user->last_name;
                })
                ->editColumn('user_email', function ($row) {
                    return $row->user->email;
                })
                ->addColumn('order_id', function ($row) {
                    return $row->order_id;
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('F j, Y');
                })
                ->addColumn('action', function ($row) {
                    $edit = '<a href="' . route('admin.veriffdata.show', $row->id) . '" class="btn rounded-pill btn-icon btn-outline-primary me-2"><i class="bx bxs-show"></i></a>';
                    return $edit;
                })
                ->rawColumns(['action', 'user_name', 'user_email', 'status'])
                ->make(true);
        }

        return view('admin.veriffdata.index');
    }

    public function show($id)
    {
        $veriffData = VeriffData::findOrFail($id);
        return view('admin.veriffdata.show', compact('veriffData'));
    }
}
