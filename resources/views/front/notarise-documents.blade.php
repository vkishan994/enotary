@extends('front.layouts.common')
@section('content')

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
                        <option value="individual" selected>Individual Notary Public</option>
                        <option value="business">Business Notary Public</option>
                        <option value="corporate">Corporate Notary Service</option>
                    </select>
                    <h2 class="section-title">Documents you would like notarised</h2>
                    <div class="documents-grid">
                        <div class="document-option">
                            <input type="radio" id="passport" name="document" value="passport" data-price="79" checked>
                            <label for="passport" class="document-label">Passport</label>
                        </div>
                        <div class="document-option">
                            <input type="radio" id="proof" name="document" value="proof" data-price="59">
                            <label for="proof" class="document-label">Proof of identity declarations</label>
                        </div>
                        <div class="document-option">
                            <input type="radio" id="powers" name="document" value="powers" data-price="89">
                            <label for="powers" class="document-label">Powers of attorney</label>
                        </div>
                        <div class="document-option">
                            <input type="radio" id="consent" name="document" value="consent" data-price="69">
                            <label for="consent" class="document-label">Consent letters</label>
                        </div>
                    </div>
                    <div class="total-section">
                        Total: £<span id="totalAmount">79.00</span>
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
