    <!-- Enable 2FA Modal -->
    <div class="modal fade" id="enable2faModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Enable Two-Factor Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body text-center">

                    <p class="mb-2">
                        Scan this QR code with <strong>Google Authenticator</strong>
                    </p>

                    <!-- QR Code -->
                    <div id="qrImage" class="mb-3">
                        <!-- QR will be injected via JS -->
                    </div>

                    <!-- Manual Key -->
                    <p class="small">
                        <strong>Manual Key:</strong>
                        <br>
                        <span id="secretKey" class="fw-bold"></span>
                    </p>

                    <hr>

                    <!-- OTP Input -->
                    <div class="mb-3">
                        <label class="form-label">Enter 6-digit OTP</label>
                        <input type="text" id="otp" class="form-control text-center" placeholder="123456"
                            maxlength="6" autocomplete="one-time-code">
                        <div id="otpError" class="text-danger small mt-1 d-none"></div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="verify2faBtn">
                        Verify & Enable
                    </button>
                </div>

            </div>
        </div>
    </div>
