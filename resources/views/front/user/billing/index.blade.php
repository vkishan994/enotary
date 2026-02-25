@extends('front.layouts.common')

@section('content')
    @include('front.layouts.dashboard.sidebar')

    <main class="main-content">

        <div class="document-upload document-pending">
            <div class="section-title mb-3">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h4>Billing</h4>
                    </div>
                </div>
            </div>

            <x-alert type="success" :message="session('success')" />
            <x-alert type="danger" :message="session('error')" />

            <div class="card">
                <div class="card-body" style="min-height: 400px;">

                    @if ($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>View Invoice</th>
                                        <th>Download Invoice</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>

                                            <td>
                                                {{ $order->created_at->format('d M Y') }}
                                            </td>

                                            <td>
                                                £{{ number_format($order->amount, 2) }}
                                            </td>

                                            <td>
                                                <span class="badge bg-success">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($order->invoice_file_path)
                                                    <a href="{{ asset('storage/' . $order->invoice_file_path) }}"
                                                        target="_blank" class="action-btn" title="Open in new tab">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">Not Available</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($order->invoice_file_path)
                                                    <a href="{{ route('user.invoice.download', $order->id) }}"
                                                        class="action-btn">
                                                        <i class="fas fa-download"></i>

                                                    </a>
                                                @else
                                                    <span class="text-muted">Not Available</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            No invoices available yet.
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </main>
@endsection
