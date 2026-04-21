@extends('admin.layouts.common')
@section('title', 'Dashboard')

@section('content')

    <div class="row">

        <!-- ORDERS -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card stat-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon text-primary">
                            <i class="bx bx-cart"></i>
                        </div>
                        <h5 class="mb-0">Orders</h5>
                    </div>

                    <select class="form-select w-auto dashboard-filter" data-type="orders">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">Current Week</option>
                        <option value="last_week">Last Week</option>
                        <option value="month">Current Month</option>
                        <option value="all">All Time</option>
                    </select>

                </div>

                <div class="card-body">

                    <h3 class="mb-3 stat-number" id="ordersCount">{{ number_format($ordersCount ?? 0) }}</h3>

                    <div id="ordersChart" class="chart-box"></div>

                </div>
            </div>
        </div>


        <!-- CUSTOMERS -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card stat-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon text-success">
                            <i class="bx bx-user"></i>
                        </div>
                        <h5 class="mb-0">Customers</h5>
                    </div>

                    <select class="form-select w-auto dashboard-filter" data-type="customers">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">Current Week</option>
                        <option value="last_week">Last Week</option>
                        <option value="month">Current Month</option>
                        <option value="all">All Time</option>
                    </select>

                </div>

                <div class="card-body">

                    <h3 class="mb-3 stat-number" id="customersCount">{{ number_format($customersCount ?? 0) }}</h3>

                </div>
            </div>
        </div>


        <!-- TRANSACTIONS -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card stat-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon text-warning">
                            <i class="bx bx-credit-card"></i>
                        </div>
                        <h5 class="mb-0">Transactions</h5>
                    </div>

                    <select class="form-select w-auto dashboard-filter" data-type="transactions">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">Current Week</option>
                        <option value="last_week">Last Week</option>
                        <option value="month">Current Month</option>
                        <option value="all">All Time</option>
                    </select>

                </div>

                <div class="card-body">

                    <h3 class="mb-3 stat-number" id="transactionsTotal">£
                        {{ number_format($transactionsTotal ?? 0, 2) }}
                    </h3>

                </div>
            </div>
        </div>

        <!-- MEETINGS TODAY -->
        <div class="col-lg-3 col-md-12 mb-4">
            <div class="card stat-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon text-success">
                            <i class="bx bx-calendar"></i>
                        </div>
                        <h5 class="mb-0">Meetings Today</h5>
                    </div>

                </div>

                <div class="card-body">

                    <h3 class="mb-3 stat-number" id="meetingsCount">
                        {{ number_format($meetingsCount ?? 0) }}
                    </h3>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-12 mb-4">
            <div class="card stat-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon text-warning">
                            <i class="bx bx-file"></i>
                        </div>
                        <h5 class="mb-0">Pending Doc Verification</h5>
                    </div>

                </div>

                <div class="card-body">

                    <h3 class="mb-3 stat-number" id="pendingDocumentsCount">
                        {{ number_format($pendingDocumentVerification ?? 0) }}
                    </h3>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-12 mb-4">
            <div class="card stat-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon text-success">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <h5 class="mb-0">Verified Documents</h5>
                    </div>


                </div>

                <div class="card-body">

                    <h3 class="mb-3 stat-number" id="verifiedDocumentsCount">
                        {{ number_format($ordersWithUnverifiedDocs ?? 0) }}
                    </h3>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-12 mb-4">
            <div class="card stat-card">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon text-warning">
                            <i class="bx bx-upload"></i>
                        </div>
                        <h5 class="mb-0">Pending Upload Documents</h5>
                    </div>

                </div>

                <div class="card-body">

                    <h3 class="mb-3 stat-number" id="pendingUploadDocumentsCount">
                        {{ number_format($pendingUploadDocuments ?? 0) }}
                    </h3>

                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <!-- Charts -->
    <script>
        var ordersChart;
        var customersChart;
        var transactionsChart;

        $('.dashboard-filter').on('change', function() {

            let filter = $(this).val();
            let type = $(this).data('type');

            $.ajax({

                url: "{{ route('admin.dashboard.filter') }}",
                type: "GET",
                data: {
                    filter: filter,
                    type: type
                },

                success: function(res) {

                    if (res.type == 'orders') {
                        $('#ordersCount').text(res.count.toLocaleString());
                    }

                    if (res.type == 'customers') {
                        $('#customersCount').text(res.count.toLocaleString());
                    }

                    if (res.type == 'transactions') {
                        $('#transactionsTotal').text('£ ' + Number(res.total).toLocaleString());
                    }

                }

            });

        });
    </script>
@endpush
