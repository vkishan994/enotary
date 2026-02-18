{{-- veriff view detials modal --}}
<div class="modal fade" id="veriffModal" tabindex="-1" aria-labelledby="veriffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-dark">
                    Identity Verification Summary
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="veriff-card">
                            <div class="label">Status</div>
                            <div class="value">
                                {!! veriffStatus($selectedOrder->veriffData->status ?? null) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="veriff-card">
                            <div class="label">Session ID</div>
                            <div class="value">
                                {{ $selectedOrder->veriffData->session_id ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="veriff-card">
                            <div class="label">Veriff Decision</div>
                            <div class="value">
                                {{ $selectedOrder->veriffData->veriff_decision ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="veriff-card">
                            <div class="label">Verified At</div>
                            <div class="value">
                                {{ isset($selectedOrder->veriffData->veriff_verified_at)
                                    ? \Carbon\Carbon::parse($selectedOrder->veriffData->veriff_verified_at)->format('d M Y, h:i A')
                                    : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="veriff-card">
                            <div class="label">Veriff Reason</div>
                            <div class="value">
                                {{ $selectedOrder->veriffData->veriff_reason ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4"
                    data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
