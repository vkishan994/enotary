@extends('admin.layouts.common')
@section('title', 'Dashboard')

@section('css')
    <style>
        .stat-card {
            border: none !important;
            border-radius: 15px !important;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
            background: #fff;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            z-index: 10;
        }

        .stat-card.card-1::before { background: linear-gradient(90deg, #696cff 0%, #afb1ff 100%); } /* Indigo */
        .stat-card.card-2::before { background: linear-gradient(90deg, #00b09b 0%, #96c93d 100%); } /* Teal-Green */
        .stat-card.card-3::before { background: linear-gradient(90deg, #ff512f 0%, #dd2476 100%); } /* Red-Pink */
        .stat-card.card-4::before { background: linear-gradient(90deg, #2193b0 0%, #6dd5ed 100%); } /* Blue */
        .stat-card.card-5::before { background: linear-gradient(90deg, #f09819 0%, #edde5d 100%); } /* Orange */
        .stat-card.card-6::before { background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%); } /* Green */
        .stat-card.card-7::before { background: linear-gradient(90deg, #8e2de2 0%, #4a00e0 100%); } /* Purple */

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px 0 rgba(0, 0, 0, 0.1) !important;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 22px;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }

        .bg-label-primary .stat-icon { background: rgba(105, 108, 255, 0.15); }
        .bg-label-success .stat-icon { background: rgba(113, 221, 55, 0.15); }
        .bg-label-warning .stat-icon { background: rgba(255, 171, 0, 0.15); }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #566a7f;
            margin-top: 5px;
        }

        .card-header {
            padding: 1.2rem 1.2rem 0.4rem 1.2rem !important;
            background: transparent !important;
            border: none !important;
        }

        .card-body {
            padding: 0.4rem 1.2rem 1.2rem 1.2rem !important;
        }

        .dashboard-filter {
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 0.85rem;
            border: 1px solid #eceef1;
            font-weight: 500;
            color: #697a8d;
            background-color: #f5f5f9;
        }

     
    </style>
@endsection

@section('content')


    <div class="row">

        <!-- ORDERS -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card stat-card card-1 h-100">

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
            <div class="card stat-card card-2 h-100">

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
            <div class="card stat-card card-3 h-100">

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
            <div class="card stat-card card-4 h-100">

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
            <div class="card stat-card card-5 h-100">

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
            <div class="card stat-card card-6 h-100">

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
            <div class="card stat-card card-7 h-100">

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
