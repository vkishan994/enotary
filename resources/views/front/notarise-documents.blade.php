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

                <!-- Stripe Card Element -->
                <div id="payment-element-container" class="mt-4" style="display: none;">
                    <label class="form-label">Card Details</label>
                    <div id="card-element" class="form-control" style="height: 40px; padding-top: 10px;"></div>
                    <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                </div>

                <button class="place-order-btn mt-3" id="placeOrderBtn">
                    Place Order
                </button>
            </div>
        </div>
    </div>
</main>
<!-- Main content end -->

@endsection

@section('js')
<script src="https://js.stripe.com/v3/"></script>
<script>
    $(document).ready(function() {
        const stripe = Stripe("{{ getValuesByKey('stripe_public_key') }}");
        let elements;
        let cardElement;

        function initStripe() {
            elements = stripe.elements();
            cardElement = elements.create('card');
            cardElement.mount('#card-element');
            $('#payment-element-container').show();
        }

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

                        // Initialize Stripe if not already done
                        if (!cardElement) initStripe();
                    } else {
                        $grid.html('<p class="text-danger">No documents found for this service type.</p>');
                    }
                }
            });
        });

        $(document).on('change', 'input[name="document"]', function() {
            const price = $(this).data('price') || 0;
            $('#totalAmount').text(parseFloat(price).toFixed(2));
        });

        $('#placeOrderBtn').on('click', async function(e) {
            e.preventDefault();
            const $btn = $(this);
            const documentId = $('input[name="document"]:checked').val();
            const serviceTypeId = $('#notaryService').val();

            if (!documentId || !serviceTypeId) {
                alert('Please select a service and a document.');
                return;
            }

            $btn.prop('disabled', true).text('Processing...');

            try {
                // 1. Create Checkout / Payment Intent on server
                const response = await $.ajax({
                    url: "{{ route('user.checkout') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        document_id: documentId,
                        service_type_id: serviceTypeId
                    }
                });

                if (response.success) {
                    // 2. Confirm payment with Stripe
                    const result = await stripe.confirmCardPayment(response.client_secret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: {
                                name: "{{ auth()->user()->name }}"
                            }
                        }
                    });

                    if (result.error) {
                        $('#card-errors').text(result.error.message);
                        $btn.prop('disabled', false).text('Place Order');
                    } else {
                        // 3. Redirect to success page
                        window.location.href = "{{ route('user.payment-success') }}?payment_intent=" + result.paymentIntent.id;
                    }
                }
            } catch (err) {
                console.error(err);
                alert(err.responseJSON ? err.responseJSON.message : 'An error occurred.');
                $btn.prop('disabled', false).text('Place Order');
            }
        });
    });
</script>
@endsection
