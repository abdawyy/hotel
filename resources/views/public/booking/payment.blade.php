@extends('layouts.public')

@section('title', 'Payment - ' . config('app.name'))

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('booking.confirmation', $booking->id) }}">Booking #{{ $booking->booking_number }}</a></li>
            <li class="breadcrumb-item active">Payment</li>
        </ol>
    </nav>

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Payment Header -->
            <div class="text-center mb-5">
                <div class="mb-3">
                    <i class="bi bi-credit-card-2-front text-primary" style="font-size: 4rem;"></i>
                </div>
                <h1 class="h2 fw-bold">Complete Your Payment</h1>
                <p class="text-muted">Secure payment for booking <strong class="text-primary">{{ $booking->booking_number }}</strong></p>
            </div>

            <div class="row g-4">
                <!-- Payment Form -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-primary text-white rounded-top-4 py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-lock me-2"></i>Secure Payment
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Payment Method Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted small text-uppercase">Choose Payment Method</label>
                                <div class="payment-methods">
                                    <!-- Credit/Debit Card Option (Stripe) -->
                                    <div class="form-check payment-method-option active mb-3" data-method="stripe">
                                        <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe" checked>
                                        <label class="form-check-label d-flex align-items-center" for="stripe">
                                            <div class="payment-icons me-3">
                                                <i class="bi bi-credit-card-fill fs-4 text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>Credit / Debit Card</strong>
                                                <small class="d-block text-muted">Visa, Mastercard, American Express</small>
                                            </div>
                                            <div class="ms-auto d-flex gap-1">
                                                <img src="https://img.icons8.com/color/32/visa.png" alt="Visa" height="24">
                                                <img src="https://img.icons8.com/color/32/mastercard.png" alt="Mastercard" height="24">
                                                <img src="https://img.icons8.com/color/32/amex.png" alt="Amex" height="24">
                                            </div>
                                        </label>
                                    </div>

                                    <!-- PayPal Option -->
                                    <div class="form-check payment-method-option" data-method="paypal">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                        <label class="form-check-label d-flex align-items-center" for="paypal">
                                            <div class="payment-icons me-3">
                                                <i class="bi bi-paypal fs-4 text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>PayPal</strong>
                                                <small class="d-block text-muted">Pay securely with your PayPal account</small>
                                            </div>
                                            <div class="ms-auto">
                                                <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" alt="PayPal" height="24">
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Amount Input -->
                            <div class="mb-4">
                                <label for="payment_amount" class="form-label fw-semibold text-muted small text-uppercase">
                                    Payment Amount
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        {{ \App\Models\Setting::getValue('currency_symbol', '$') }}
                                    </span>
                                    <input type="number" 
                                           class="form-control border-start-0 ps-0" 
                                           id="payment_amount" 
                                           name="amount"
                                           value="{{ number_format($remainingBalance, 2, '.', '') }}"
                                           min="0.01"
                                           max="{{ $remainingBalance }}"
                                           step="0.01"
                                           required>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Remaining balance: <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($remainingBalance, 2) }}</strong>
                                </div>
                            </div>

                            <!-- Stripe Card Element Container -->
                            <div id="stripe-container" class="mb-4">
                                <label class="form-label fw-semibold text-muted small text-uppercase">Card Details</label>
                                <div id="card-element" class="form-control py-3" style="height: auto;"></div>
                                <div id="card-errors" class="text-danger small mt-2"></div>
                                <button type="button" id="stripe-pay-button" class="btn btn-primary btn-lg w-100 mt-3">
                                    <i class="bi bi-lock me-2"></i>Pay with Card
                                </button>
                            </div>

                            <!-- PayPal Button Container -->
                            <div id="paypal-button-container" class="mb-3 d-none"></div>

                            <!-- Loading State -->
                            <div id="payment-loading" class="text-center py-4 d-none">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Processing...</span>
                                </div>
                                <p class="mb-0 text-muted">Processing your payment...</p>
                            </div>

                            <!-- Error Message -->
                            <div id="payment-error" class="alert alert-danger d-none">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <span id="payment-error-message"></span>
                            </div>

                            <!-- Success Message -->
                            <div id="payment-success" class="alert alert-success d-none">
                                <i class="bi bi-check-circle me-2"></i>
                                <span id="payment-success-message"></span>
                            </div>

                            <div class="text-center mt-4">
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-lock me-1"></i>
                                    Your payment information is encrypted and secure
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Security Badges -->
                    <div class="text-center mt-4">
                        <div class="d-flex justify-content-center align-items-center gap-4">
                            <div class="text-muted">
                                <i class="bi bi-shield-check fs-4 d-block mb-1"></i>
                                <small>SSL Secure</small>
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-lock fs-4 d-block mb-1"></i>
                                <small>Encrypted</small>
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-credit-card fs-4 d-block mb-1"></i>
                                <small>Safe Payment</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                        <div class="card-header bg-light rounded-top-4 py-3">
                            <h5 class="mb-0">
                                <i class="bi bi-receipt me-2"></i>Order Summary
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Booking Info -->
                            <div class="mb-3">
                                <small class="text-muted text-uppercase fw-semibold">Booking Number</small>
                                <p class="mb-0 fw-bold text-primary">{{ $booking->booking_number }}</p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted text-uppercase fw-semibold">Guest Name</small>
                                <p class="mb-0">{{ $booking->guest_name }}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted text-uppercase fw-semibold">Check-in</small>
                                    <p class="mb-0">{{ $booking->check_in_date->format('M d, Y') }}</p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted text-uppercase fw-semibold">Check-out</small>
                                    <p class="mb-0">{{ $booking->check_out_date->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <hr>

                            <!-- Room Details -->
                            @foreach($booking->details as $detail)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span>{{ $detail->roomType->name }}</span>
                                        <small class="text-muted d-block">{{ $detail->quantity }}x · {{ $detail->nights }} night(s)</small>
                                    </div>
                                    <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($detail->subtotal, 2) }}</strong>
                                </div>
                            @endforeach

                            <hr>

                            <!-- Pricing Breakdown -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->total_price, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax</span>
                                <span>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->tax_amount, 2) }}</span>
                            </div>
                            @if($booking->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount</span>
                                    <span>-{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->discount_amount, 2) }}</span>
                                </div>
                            @endif

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <strong>Total Amount</strong>
                                <strong>{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($booking->final_amount, 2) }}</strong>
                            </div>

                            @if($paidAmount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Amount Paid</span>
                                    <span>-{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($paidAmount, 2) }}</span>
                                </div>
                            @endif

                            <hr>

                            <div class="d-flex justify-content-between align-items-center bg-primary bg-opacity-10 p-3 rounded-3">
                                <span class="fs-5 fw-bold">Balance Due</span>
                                <span class="fs-4 fw-bold text-primary">{{ \App\Models\Setting::getValue('currency_symbol', '$') }}{{ number_format($remainingBalance, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rounded-4 { border-radius: 1rem !important; }
.rounded-top-4 { border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important; }

.payment-method-option {
    border: 2px solid #e9ecef;
    border-radius: 0.75rem;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.payment-method-option:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.payment-method-option.active {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.payment-method-option .form-check-input {
    margin-top: 0.5rem;
}

.input-group-lg .form-control {
    font-size: 1.5rem;
    font-weight: 600;
}

.input-group-lg .input-group-text {
    font-size: 1.25rem;
    font-weight: 600;
}

#paypal-button-container {
    min-height: 50px;
}

#card-element {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 12px;
    background: #fff;
}

#card-element.StripeElement--focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

#card-element.StripeElement--invalid {
    border-color: #dc3545;
}

.payment-icons {
    width: 40px;
    height: 40px;
    background: #f0f4ff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency={{ $paypalCurrency }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingId = {{ $booking->id }};
    const maxAmount = {{ $remainingBalance }};
    const amountInput = document.getElementById('payment_amount');
    const loadingEl = document.getElementById('payment-loading');
    const errorEl = document.getElementById('payment-error');
    const errorMessageEl = document.getElementById('payment-error-message');
    const successEl = document.getElementById('payment-success');
    const successMessageEl = document.getElementById('payment-success-message');
    const stripeContainer = document.getElementById('stripe-container');
    const paypalContainer = document.getElementById('paypal-button-container');
    const stripePayButton = document.getElementById('stripe-pay-button');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    const paymentMethodOptions = document.querySelectorAll('.payment-method-option');

    // Initialize Stripe
    const stripe = Stripe('{{ $stripePublicKey }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#dc3545',
                iconColor: '#dc3545'
            }
        }
    });
    cardElement.mount('#card-element');

    // Handle card errors
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    function showLoading(show) {
        loadingEl.classList.toggle('d-none', !show);
        stripeContainer.style.display = show ? 'none' : 'block';
        paypalContainer.style.display = show ? 'none' : 'block';
    }

    function showError(message) {
        errorEl.classList.remove('d-none');
        errorMessageEl.textContent = message;
        successEl.classList.add('d-none');
    }

    function showSuccess(message) {
        successEl.classList.remove('d-none');
        successMessageEl.textContent = message;
        errorEl.classList.add('d-none');
    }

    function hideMessages() {
        errorEl.classList.add('d-none');
        successEl.classList.add('d-none');
    }

    // Payment method selection
    paymentMethodRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            const method = this.value;
            
            // Update active class
            paymentMethodOptions.forEach(function(opt) {
                opt.classList.remove('active');
            });
            this.closest('.payment-method-option').classList.add('active');

            // Toggle containers
            if (method === 'stripe') {
                stripeContainer.classList.remove('d-none');
                paypalContainer.classList.add('d-none');
            } else {
                stripeContainer.classList.add('d-none');
                paypalContainer.classList.remove('d-none');
            }
        });
    });

    // Validate amount on input
    amountInput.addEventListener('input', function() {
        let value = parseFloat(this.value);
        if (value > maxAmount) {
            this.value = maxAmount.toFixed(2);
        }
        if (value < 0.01) {
            this.value = '0.01';
        }
    });

    // Stripe Payment Handler
    stripePayButton.addEventListener('click', async function() {
        hideMessages();
        const amount = parseFloat(amountInput.value);

        if (isNaN(amount) || amount <= 0 || amount > maxAmount) {
            showError('Please enter a valid payment amount.');
            return;
        }

        showLoading(true);
        stripePayButton.disabled = true;

        try {
            // Create payment intent
            const response = await fetch('{{ route("stripe.create-payment-intent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    amount: amount
                })
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to create payment.');
            }

            // Confirm payment with Stripe
            const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
                payment_method: {
                    card: cardElement
                }
            });

            if (error) {
                throw new Error(error.message);
            }

            if (paymentIntent.status === 'succeeded') {
                // Confirm payment on server
                const confirmResponse = await fetch('{{ route("stripe.confirm-payment") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        payment_intent_id: paymentIntent.id
                    })
                });

                const confirmData = await confirmResponse.json();

                if (confirmData.success) {
                    showSuccess(confirmData.message || 'Payment completed successfully!');
                    setTimeout(function() {
                        window.location.href = confirmData.redirect;
                    }, 2000);
                } else {
                    throw new Error(confirmData.message || 'Payment confirmation failed.');
                }
            }
        } catch (err) {
            showLoading(false);
            showError(err.message || 'An error occurred. Please try again.');
            stripePayButton.disabled = false;
        }
    });

    // Initialize PayPal Buttons
    paypal.Buttons({
        style: {
            layout: 'vertical',
            color: 'blue',
            shape: 'rect',
            label: 'pay',
            height: 50
        },

        createOrder: function(data, actions) {
            hideMessages();
            const amount = parseFloat(amountInput.value);

            if (isNaN(amount) || amount <= 0 || amount > maxAmount) {
                showError('Please enter a valid payment amount.');
                return Promise.reject(new Error('Invalid amount'));
            }

            showLoading(true);

            return fetch('{{ route("paypal.create-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    amount: amount
                })
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                showLoading(false);
                if (data.success && data.orderID) {
                    return data.orderID;
                } else {
                    showError(data.message || 'Failed to create order.');
                    return Promise.reject(new Error(data.message));
                }
            })
            .catch(function(err) {
                showLoading(false);
                showError('An error occurred. Please try again.');
                return Promise.reject(err);
            });
        },

        onApprove: function(data, actions) {
            showLoading(true);
            hideMessages();

            return fetch('{{ route("paypal.capture-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_id: data.orderID
                })
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(result) {
                showLoading(false);
                if (result.success) {
                    showSuccess(result.message || 'Payment completed successfully!');
                    paypalContainer.style.display = 'none';
                    
                    setTimeout(function() {
                        window.location.href = result.redirect;
                    }, 2000);
                } else {
                    showError(result.message || 'Payment failed.');
                }
            })
            .catch(function(err) {
                showLoading(false);
                showError('An error occurred while processing payment.');
            });
        },

        onCancel: function(data) {
            showError('Payment was cancelled. You can try again when ready.');
        },

        onError: function(err) {
            showLoading(false);
            showError('An error occurred with PayPal. Please try again.');
            console.error('PayPal Error:', err);
        }
    }).render('#paypal-button-container');
});
</script>
@endsection
