const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

document
    .getElementById("google2fa_status")
    .addEventListener("change", function () {
        const isChecked = this.checked;

        if (isChecked) {
            // ENABLE 2FA
            fetch(user2faGenerate + "?action=enable", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    document.getElementById("qrImage").innerHTML = data.qr;
                    document.getElementById("secretKey").innerText =
                        data.secret;

                    new bootstrap.Modal(
                        document.getElementById("enable2faModal"),
                    ).show();
                })
                .catch(() => {
                    Swal.fire("Error", "Unable to enable 2FA.", "error");
                    this.checked = false;
                });
        } else {
            // DISABLE 2FA
            Swal.fire({
                title: "Disable Two-Factor Authentication?",
                text: "This will reduce account security.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Disable",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(user2faGenerate + "?action=disable", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "X-Requested-With": "XMLHttpRequest",
                        },
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.success) {
                                Swal.fire(
                                    "Disabled",
                                    data.message,
                                    "success",
                                ).then(() => {
                                    // Reload the page after user closes the alert
                                    location.reload();
                                });
                            } else {
                                throw new Error();
                            }
                        })
                        .catch(() => {
                            Swal.fire(
                                "Error",
                                "Unable to disable 2FA.",
                                "error",
                            );
                            this.checked = true;
                        });
                } else {
                    this.checked = true;
                }
            });
        }
    });

const verifyBtn = document.getElementById("verify2faBtn");

verifyBtn.addEventListener("click", function () {
    const otpInput = document.getElementById("otp");
    const otp = otpInput.value.trim();
    const errorBox = document.getElementById("otpError");

    if (!otp || otp.length !== 6) {
        errorBox.textContent = "Please enter a valid 6-digit OTP";
        errorBox.classList.remove("d-none");
        return;
    }

    errorBox.classList.add("d-none");

    fetch(userVerifyTwoFactor, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({
            otp,
        }),
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                //  CLOSE MODAL FIRST (IMPORTANT)
                const modalEl = document.getElementById("enable2faModal");
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                // SWEETALERT SUCCESS
                Swal.fire({
                    icon: "success",
                    title: "2FA Enabled",
                    text: "Two-Factor Authentication has been enabled successfully.",
                    confirmButtonText: "OK",
                }).then(() => {
                    location.reload();
                });
            } else {
                errorBox.textContent = data.message || "Invalid OTP";
                errorBox.classList.remove("d-none");
            }
        })
        .catch(() => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Something went wrong. Please try again.",
            });
        });
});
