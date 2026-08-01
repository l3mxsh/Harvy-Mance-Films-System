let currentStep = 1;
let selectedPackageId = null;
let selectedPackageData = null;
let selectedAddonIds = [];
let dateCheckTimeout = null;
let emailExists = false;
let emailExistsMessage = '';

function selectPackage(el) {
    document.querySelectorAll('.package-card').forEach(function(card) {
        card.classList.remove('selected');
        card.querySelector('.check-indicator i').classList.add('d-none');
    });

    el.classList.add('selected');
    el.querySelector('.check-indicator i').classList.remove('d-none');

    selectedPackageId = el.dataset.packageId;
    selectedPackageData = {
        id: el.dataset.packageId,
        price: parseFloat(el.dataset.price),
        name: el.querySelector('h6').textContent.trim()
    };

    document.getElementById('selectedPackageId').value = selectedPackageId;
    updatePriceSummary();
}

function toggleAddon(checkbox) {
    var addonId = checkbox.value;
    var addonCard = checkbox.closest('.addon-card');

    if (checkbox.checked) {
        addonCard.classList.add('selected');
        selectedAddonIds.push(addonId);
    } else {
        addonCard.classList.remove('selected');
        selectedAddonIds = selectedAddonIds.filter(function(id) {
            return id !== addonId;
        });
    }

    syncAddonInputs();
    updatePriceSummary();
}

function syncAddonInputs() {
    var container = document.getElementById('addonInputsContainer');
    container.innerHTML = '';
    selectedAddonIds.forEach(function(id) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'addon_ids[]';
        input.value = id;
        container.appendChild(input);
    });
}

function updatePriceSummary() {
    var packagePrice = selectedPackageData ? selectedPackageData.price : 0;
    var addonsTotal = 0;
    var addonNames = [];

    selectedAddonIds.forEach(function(addonId) {
        var card = document.querySelector('.addon-card[data-addon-id="' + addonId + '"]');
        if (card) {
            var price = parseFloat(card.dataset.price);
            addonsTotal += price;
            addonNames.push(card.querySelector('.fw-semibold').textContent.trim());
        }
    });

    var total = packagePrice + addonsTotal;
    document.getElementById('hiddenTotalPrice').value = total.toFixed(2);

    // Step 1 summary
    var summaryPackage = document.getElementById('summaryPackage');
    var summaryPackagePrice = document.getElementById('summaryPackagePrice');
    if (selectedPackageData) {
        summaryPackage.style.display = 'flex';
        document.getElementById('summaryPackageName').textContent = selectedPackageData.name;
        summaryPackagePrice.style.display = 'flex';
        document.getElementById('summaryPackagePriceLabel').textContent = 'Price';
        document.getElementById('summaryPackagePriceValue').textContent = formatPeso(packagePrice);
    } else {
        summaryPackage.style.display = 'none';
        summaryPackagePrice.style.display = 'none';
    }

    // Add-ons summary
    var addonsHeader = document.getElementById('summaryAddonsHeader');
    var addonsContainer = document.getElementById('summaryAddonsContainer');
    var existingAddonRows = addonsContainer.querySelectorAll('.addon-summary-row');
    existingAddonRows.forEach(function(row) { row.remove(); });

    if (selectedAddonIds.length > 0) {
        addonsHeader.style.display = 'flex';
        selectedAddonIds.forEach(function(addonId) {
            var card = document.querySelector('.addon-card[data-addon-id="' + addonId + '"]');
            if (card) {
                var row = document.createElement('div');
                row.className = 'summary-row addon-summary-row';
                row.innerHTML = '<span>' + card.querySelector('.fw-semibold').textContent.trim() + '</span><span>' + formatPeso(parseFloat(card.dataset.price)) + '</span>';
                addonsContainer.appendChild(row);
            }
        });
    } else {
        addonsHeader.style.display = 'none';
    }

    document.getElementById('summaryTotal').textContent = formatPeso(total);

    // Step 2 & 3 summaries (elements may not exist if sidebar was removed)
    if (selectedPackageData) {
        var s2pkg = document.getElementById('summary2PackageName');
        var s3pkg = document.getElementById('summary3PackageName');
        if (s2pkg) s2pkg.textContent = selectedPackageData.name;
        if (s3pkg) s3pkg.textContent = selectedPackageData.name;
    }
    var s2addons = document.getElementById('summary2AddonsCount');
    var s3addons = document.getElementById('summary3AddonsCount');
    var s2total = document.getElementById('summary2Total');
    var s3total = document.getElementById('summary3Total');
    if (s2addons) s2addons.textContent = selectedAddonIds.length + ' items';
    if (s3addons) s3addons.textContent = selectedAddonIds.length + ' items';
    if (s2total) s2total.textContent = formatPeso(total);
    if (s3total) s3total.textContent = formatPeso(total);

    // Step 4 confirmation
    updateConfirmationSummary(packagePrice, addonsTotal, total, addonNames);
}

function updateConfirmationSummary(packagePrice, addonsTotal, total, addonNames) {
    if (selectedPackageData) {
        document.getElementById('confirmPackageName').textContent = selectedPackageData.name;
        document.getElementById('confirmPackagePrice').textContent = formatPeso(packagePrice);

        var pkgCard = document.querySelector('.package-card[data-package-id="' + selectedPackageId + '"]');
        if (pkgCard) {
            var services = [];
            pkgCard.querySelectorAll('.service-list li').forEach(function(li) {
                services.push(li.textContent.trim());
            });
            document.getElementById('confirmServices').textContent = services.join(', ');
        }
    }

    var addonsSection = document.getElementById('confirmAddonsSection');
    if (selectedAddonIds.length > 0) {
        addonsSection.style.display = 'block';
        document.getElementById('confirmAddons').textContent = addonNames.join(', ');
        document.getElementById('confirmAddonsPrice').textContent = formatPeso(addonsTotal);
    } else {
        addonsSection.style.display = 'none';
    }

    document.getElementById('confirmTotalAmount').textContent = formatPeso(total);
    document.getElementById('confirmDownpayment').textContent = formatPeso(Math.round(total * 0.30 * 100) / 100);
    document.getElementById('confirmBalance').textContent = formatPeso(Math.round(total * 0.70 * 100) / 100);
}

function updateConfirmationCustomerInfo() {
    document.getElementById('confirmName').textContent = document.getElementById('clientName').value || '-';
    document.getElementById('confirmEmail').textContent = document.getElementById('clientEmail').value || '-';
    document.getElementById('confirmPhone').textContent = document.getElementById('clientPhone').value || '-';
}

function updateConfirmationEventInfo() {
    var eventType = document.getElementById('eventType').value;
    var eventDate = document.getElementById('eventDate').value;
    var eventTime = document.getElementById('eventTime').value;
    var venue = document.getElementById('eventVenue').value;
    var eventAddress = document.getElementById('eventAddress').value;
    var eventDesc = document.getElementById('eventDescription').value;

    document.getElementById('confirmEventType').textContent = eventType || '-';
    document.getElementById('confirmEventDate').textContent = eventDate ? formatDate(eventDate) : '-';
    document.getElementById('confirmEventTime').textContent = eventTime || '-';
    document.getElementById('confirmVenue').textContent = venue || '-';

    var addressEventRow = document.getElementById('confirmAddressEventRow');
    if (eventAddress) {
        addressEventRow.style.display = 'flex';
        document.getElementById('confirmEventAddress').textContent = eventAddress;
    } else {
        addressEventRow.style.display = 'none';
    }

    var descRow = document.getElementById('confirmDescRow');
    if (eventDesc) {
        descRow.style.display = 'flex';
        document.getElementById('confirmEventDesc').textContent = eventDesc;
    } else {
        descRow.style.display = 'none';
    }
}

function formatPeso(amount) {
    return '\u20B1' + parseFloat(amount).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatDate(dateStr) {
    var options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateStr).toLocaleDateString('en-US', options);
}

function goToStep(step) {
    if (step === currentStep) return;

    if (step > currentStep) {
        for (var i = currentStep; i < step; i++) {
            if (!validateStep(i)) return;
        }
    }

    if (step === 4) {
        updateConfirmationCustomerInfo();
        updateConfirmationEventInfo();
    }

    document.querySelectorAll('.step-content').forEach(function(el) {
        el.classList.remove('active');
    });
    document.getElementById('step' + step).classList.add('active');

    var steps = document.querySelectorAll('.stepper-step');
    var connectors = document.querySelectorAll('.stepper-connector');

    steps.forEach(function(s) {
        var sStep = parseInt(s.dataset.step);
        s.classList.remove('active', 'completed');
        if (sStep === step) {
            s.classList.add('active');
        } else if (sStep < step) {
            s.classList.add('completed');
        }
    });

    connectors.forEach(function(c, index) {
        c.classList.remove('completed');
        if (index < step - 1) {
            c.classList.add('completed');
        }
    });

    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    if (step === 1) {
        if (!selectedPackageId) {
            showValidationAlert('Please select a package to continue.');
            return false;
        }
        return true;
    }

    if (step === 2) {
        var name = document.getElementById('clientName').value.trim();
        var email = document.getElementById('clientEmail').value.trim();
        var phone = document.getElementById('clientPhone').value.trim();

        if (!name) {
            showValidationAlert('Please enter your full name.');
            return false;
        }
        if (!email || !isValidEmail(email)) {
            showValidationAlert('Please enter a valid email address.');
            return false;
        }
        if (emailExists) {
            showValidationAlert(emailExistsMessage || 'This email is already used in an existing booking.');
            return false;
        }
        if (!phone) {
            showValidationAlert('Please enter your contact number.');
            return false;
        }
        if (!isValidPhone(phone)) {
            showValidationAlert('Please enter a valid contact number in the format 0912-345-6789.');
            return false;
        }
        return true;
    }

    if (step === 3) {
        var eventType = document.getElementById('eventType').value;
        var eventDate = document.getElementById('eventDate').value;
        var eventTime = document.getElementById('eventTime').value;
        var venue = document.getElementById('eventVenue').value;

        if (!eventType) {
            showValidationAlert('Please select an event type.');
            return false;
        }
        if (!eventDate) {
            showValidationAlert('Please select an event date.');
            return false;
        }
        if (!eventTime) {
            showValidationAlert('Please select an event start time.');
            return false;
        }
        if (!venue) {
            showValidationAlert('Please enter the event venue.');
            return false;
        }
        return true;
    }

    return true;
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^09\d{2}-\d{3}-\d{4}$/.test(phone);
}

function checkEmailExists() {
    var emailInput = document.getElementById('clientEmail');
    var email = emailInput.value.trim();
    var statusDiv = document.getElementById('emailStatus');

    if (!email || !isValidEmail(email)) {
        emailExists = false;
        emailExistsMessage = '';
        emailInput.classList.remove('is-invalid');
        if (statusDiv) statusDiv.innerHTML = '';
        return;
    }

    fetch('/api/booking/check-email?email=' + encodeURIComponent(email))
        .then(function(response) { return response.json(); })
        .then(function(data) {
            emailExists = data.exists;
            emailExistsMessage = data.message || '';
            if (data.exists) {
                emailInput.classList.add('is-invalid');
                if (statusDiv) statusDiv.innerHTML = '<span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>' + data.message + '</span>';
            } else {
                emailInput.classList.remove('is-invalid');
                if (statusDiv) statusDiv.innerHTML = '';
            }
        })
        .catch(function() {
            emailExists = false;
            emailExistsMessage = '';
        });
}

function formatPhoneInput(input) {
    var digits = input.value.replace(/\D/g, '').slice(0, 11);
    var formatted = '';

    if (digits.length > 0) {
        if (digits.length <= 4) {
            formatted = digits;
        } else if (digits.length <= 7) {
            formatted = digits.slice(0, 4) + '-' + digits.slice(4);
        } else {
            formatted = digits.slice(0, 4) + '-' + digits.slice(4, 7) + '-' + digits.slice(7);
        }
    }

    input.value = formatted;
}

function showValidationAlert(message) {
    var existingAlert = document.querySelector('.validation-toast');
    if (existingAlert) existingAlert.remove();

    var toast = document.createElement('div');
    toast.className = 'validation-toast';
    toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:#dc3545;color:#fff;padding:1rem 1.5rem;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);font-size:0.9rem;max-width:400px;animation:fadeInOut 3s ease forwards;';
    toast.innerHTML = '<i class="bi bi-exclamation-circle me-2"></i>' + message;
    document.body.appendChild(toast);

    setTimeout(function() {
        toast.remove();
    }, 3000);
}

function checkDateAvailability() {
    var dateInput = document.getElementById('eventDate');
    var date = dateInput.value;
    var statusDiv = document.getElementById('dateStatus');

    if (!date) {
        statusDiv.innerHTML = '';
        return;
    }

    if (dateCheckTimeout) clearTimeout(dateCheckTimeout);

    statusDiv.innerHTML = '<span class="text-muted small"><i class="bi bi-hourglass-split me-1"></i>Checking availability...</span>';

    dateCheckTimeout = setTimeout(function() {
        fetch('/api/booking/check-date?date=' + encodeURIComponent(date))
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.available) {
                    statusDiv.innerHTML = '<span class="date-available"><i class="bi bi-check-circle me-1"></i>' + data.message + '</span>';
                } else {
                    statusDiv.innerHTML = '<span class="date-unavailable"><i class="bi bi-exclamation-triangle me-1"></i>' + data.message + '</span>';
                }
                checkInventoryAvailability();
            })
            .catch(function() {
                statusDiv.innerHTML = '<span class="text-muted small">Could not check availability.</span>';
            });
    }, 500);
}

function checkInventoryAvailability() {
    var packageId = selectedPackageId;
    var eventDate = document.getElementById('eventDate').value;
    var alertDiv = document.getElementById('inventoryAlert');

    if (!packageId || !eventDate) {
        alertDiv.style.display = 'none';
        return;
    }

    var addonIdsParam = selectedAddonIds.length > 0 ? selectedAddonIds.join(',') : '';

    fetch('/api/booking/check-inventory?package_id=' + packageId + '&event_date=' + encodeURIComponent(eventDate) + '&addon_ids=' + addonIdsParam)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.available) {
                alertDiv.style.display = 'none';
            } else {
                alertDiv.style.display = 'block';
                var msg = data.message;
                if (data.unavailable_items && data.unavailable_items.length > 0) {
                    msg += '<br><strong>Unavailable items:</strong><br>';
                    data.unavailable_items.forEach(function(item) {
                        msg += '- ' + item.name + ' (Required: ' + item.required + ', Available: ' + item.available + ')<br>';
                    });
                    msg += '<small class="text-muted">Please consider choosing a different date or contacting us for assistance.</small>';
                }
                document.getElementById('inventoryAlertMessage').innerHTML = msg;
            }
        })
        .catch(function() {
            alertDiv.style.display = 'none';
        });
}

function toggleTerms() {
    var checked = document.getElementById('termsCheck').checked;
    document.getElementById('hiddenTermsAgreed').value = checked ? '1' : '0';
    document.getElementById('submitBookingBtn').disabled = !checked;
}

// ==================== OTP VERIFICATION ====================

var otpTimer = null;
var otpExpiresAt = null;

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function startOtpVerification() {
    var name = document.getElementById('clientName').value.trim();
    var email = document.getElementById('clientEmail').value.trim();

    if (!name || !email) {
        showValidationAlert('Please complete your name and email in Step 2.');
        return;
    }

    var modal = new bootstrap.Modal(document.getElementById('otpModal'));
    modal.show();

    document.getElementById('otpSendingState').style.display = 'block';
    document.getElementById('otpInputState').style.display = 'none';
    document.getElementById('otpFooter').style.display = 'none';
    document.getElementById('otpEmailDisplay').textContent = email;
    document.getElementById('otpInput').value = '';
    document.getElementById('otpError').style.display = 'none';
    document.getElementById('otpError').textContent = '';
    document.getElementById('otpSuccess').style.display = 'none';
    document.getElementById('otpSuccess').textContent = '';
    document.getElementById('otpVerifyBtn').disabled = true;

    fetch('/api/otp/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email: email, client_name: name })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        document.getElementById('otpSendingState').style.display = 'none';
        document.getElementById('otpInputState').style.display = 'block';
        document.getElementById('otpFooter').style.display = 'flex';

        if (data.success) {
            otpExpiresAt = new Date(data.expires_at);
            startOtpCountdown();
            document.getElementById('otpVerifyBtn').disabled = false;
        } else {
            document.getElementById('otpError').textContent = data.message;
            document.getElementById('otpError').style.display = 'block';
        }
    })
    .catch(function() {
        document.getElementById('otpSendingState').style.display = 'none';
        document.getElementById('otpInputState').style.display = 'block';
        document.getElementById('otpFooter').style.display = 'flex';
        document.getElementById('otpError').textContent = 'Failed to send verification code. Please try again.';
        document.getElementById('otpError').style.display = 'block';
    });
}

function startOtpCountdown() {
    if (otpTimer) clearInterval(otpTimer);
    var resendBtn = document.getElementById('otpResendBtn');
    resendBtn.disabled = true;

    otpTimer = setInterval(function() {
        var now = new Date();
        var diff = Math.max(0, Math.floor((otpExpiresAt - now) / 1000));

        if (diff <= 0) {
            clearInterval(otpTimer);
            document.getElementById('otpCountdown').textContent = 'Code expired';
            document.getElementById('otpCountdown').classList.add('text-danger');
            document.getElementById('otpVerifyBtn').disabled = true;
            resendBtn.disabled = false;
            return;
        }

        var mins = Math.floor(diff / 60);
        var secs = diff % 60;
        document.getElementById('otpCountdown').textContent = 'Expires in ' + mins + ':' + (secs < 10 ? '0' : '') + secs;
        document.getElementById('otpCountdown').classList.remove('text-danger');
    }, 1000);
}

function verifyOtp() {
    var code = document.getElementById('otpInput').value.trim();
    var email = document.getElementById('clientEmail').value.trim();

    if (code.length !== 6) {
        document.getElementById('otpError').textContent = 'Please enter the 6-digit code.';
        document.getElementById('otpError').style.display = 'block';
        return;
    }

    document.getElementById('otpVerifyBtn').disabled = true;
    document.getElementById('otpError').style.display = 'none';

    fetch('/api/otp/verify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email: email, code: code })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            document.getElementById('otpSuccess').textContent = data.message;
            document.getElementById('otpSuccess').style.display = 'block';
            document.getElementById('otpError').style.display = 'none';
            clearInterval(otpTimer);

            setTimeout(function() {
                var otpModal = bootstrap.Modal.getInstance(document.getElementById('otpModal'));
                otpModal.hide();
                document.getElementById('otpVerified').value = '1';
                document.getElementById('bookingForm').submit();
            }, 1000);
        } else {
            document.getElementById('otpError').textContent = data.message;
            document.getElementById('otpError').style.display = 'block';
            document.getElementById('otpSuccess').style.display = 'none';
            document.getElementById('otpInput').value = '';
            document.getElementById('otpInput').focus();
            document.getElementById('otpVerifyBtn').disabled = false;

            if (data.expired) {
                document.getElementById('otpResendBtn').disabled = false;
            }
        }
    })
    .catch(function() {
        document.getElementById('otpError').textContent = 'Verification failed. Please try again.';
        document.getElementById('otpError').style.display = 'block';
        document.getElementById('otpSuccess').style.display = 'none';
        document.getElementById('otpVerifyBtn').disabled = false;
    });
}

function resendOtp() {
    var email = document.getElementById('clientEmail').value.trim();
    var name = document.getElementById('clientName').value.trim();

    document.getElementById('otpError').style.display = 'none';
    document.getElementById('otpSuccess').style.display = 'none';
    document.getElementById('otpInput').value = '';
    document.getElementById('otpResendBtn').disabled = true;

    fetch('/api/otp/resend', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email: email, client_name: name })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            otpExpiresAt = new Date(data.expires_at);
            startOtpCountdown();
            document.getElementById('otpVerifyBtn').disabled = false;
            document.getElementById('otpSuccess').textContent = 'New code sent! Check your email.';
            document.getElementById('otpSuccess').style.display = 'block';
        } else {
            document.getElementById('otpError').textContent = data.message;
            document.getElementById('otpError').style.display = 'block';
            document.getElementById('otpResendBtn').disabled = false;
        }
    })
    .catch(function() {
        document.getElementById('otpError').textContent = 'Failed to resend code.';
        document.getElementById('otpError').style.display = 'block';
        document.getElementById('otpResendBtn').disabled = false;
    });
}

function cancelOtp() {
    if (otpTimer) clearInterval(otpTimer);
    var otpModal = bootstrap.Modal.getInstance(document.getElementById('otpModal'));
    otpModal.hide();
}

document.addEventListener('DOMContentLoaded', function() {
    var style = document.createElement('style');
    style.textContent = '@keyframes fadeInOut { 0% { opacity: 0; transform: translateY(-10px); } 10% { opacity: 1; transform: translateY(0); } 80% { opacity: 1; } 100% { opacity: 0; } }';
    document.head.appendChild(style);

    var clientPhone = document.getElementById('clientPhone');
    if (clientPhone) {
        clientPhone.addEventListener('input', function() {
            formatPhoneInput(this);
        });
    }

    var clientEmail = document.getElementById('clientEmail');
    if (clientEmail) {
        var emailCheckTimeout = null;
        clientEmail.addEventListener('input', function() {
            if (emailCheckTimeout) clearTimeout(emailCheckTimeout);
            emailCheckTimeout = setTimeout(checkEmailExists, 400);
        });
        if (clientEmail.value.trim()) {
            checkEmailExists();
        }
    }
});
