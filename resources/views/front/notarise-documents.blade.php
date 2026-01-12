@extends('front.layouts.common')
@section('content')
@include('front.layouts.dashboard.sidebar')
<!-- Main content start -->
<main class="main-content">
    <div class="document-pending">
        <div class="section-title">
            <h4>Notarise your documents</h4>
        </div>
        <div class="service-notary">
            <div class="form-container">
                <h2 class="section-title">Select Notary Service Type</h2>
                <select class="form-select mb-4" id="notaryService">
                    <option value="" disabled selected>Select a Service</option>
                    @foreach($notaryServiceTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                <h2 class="section-title">Documents you would like notarised</h2>
                <div class="documents-grid" id="documentsGrid">
                    <p class="text-muted">Please select a service type first.</p>
                </div>
                <div class="total-section">
                    Total: £<span id="totalAmount">0.00</span>
                </div>
                <button class="place-order-btn" id="placeOrderBtn">
                    Place Order
                </button>
            </div>
        </div>
    </div>
</main>
<!-- Main content end -->

@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#notaryService').on('change', function() {
            const serviceTypeId = $(this).val();
            const $grid = $('#documentsGrid');
            const $totalAmount = $('#totalAmount');

            $grid.html('<p class="text-muted">Loading documents...</p>');
            $totalAmount.text('0.00');

            $.ajax({
                url: "{{ route('user.get-documents') }}",
                type: 'GET',
                data: {
                    service_type_id: serviceTypeId
                },
                success: function(documents) {
                    $grid.empty();
                    if (documents.length > 0) {
                        documents.forEach((doc, index) => {
                            const checked = index === 0 ? 'checked' : '';
                            const docHtml = `
                                <div class="document-option">
                                    <input type="radio" id="doc_${doc.id}" name="document" value="${doc.id}" data-price="${doc.price}" ${checked}>
                                    <label for="doc_${doc.id}" class="document-label">${doc.name}</label>
                                </div>
                            `;
                            $grid.append(docHtml);
                        });

                        // Set initial total
                        const firstPrice = documents[0].price || 0;
                        $totalAmount.text(parseFloat(firstPrice).toFixed(2));
                    } else {
                        $grid.html('<p class="text-danger">No documents found for this service type.</p>');
                    }
                },
                error: function() {
                    $grid.html('<p class="text-danger">Error loading documents. Please try again.</p>');
                }
            });
        });

        // Handle price update on document selection
        $(document).on('change', 'input[name="document"]', function() {
            const price = $(this).data('price') || 0;
            $('#totalAmount').text(parseFloat(price).toFixed(2));
        });
    });
</script>
@endsection