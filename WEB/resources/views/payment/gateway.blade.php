<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Razorpay Payment Gateway — Sip N Bite</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #FAF7F2 0%, #F5EFEB 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .pay-card {
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 420px;
            padding: 32px 24px;
            text-align: center;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #FEE2E2;
            color: #DC2626;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .amount-box {
            background: #F8FAFC;
            border: 2px dashed #CBD5E1;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .amount-label {
            font-size: 13px;
            color: #64748B;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .amount-val {
            font-size: 36px;
            color: #0F172A;
            font-weight: 900;
            margin: 4px 0;
        }
        .desc-text {
            font-size: 14px;
            color: #475569;
            font-weight: 500;
        }
        .btn-pay {
            background: #E23744;
            color: #FFFFFF;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(226, 55, 68, 0.4);
            transition: all 0.2s ease;
        }
        .btn-pay:hover {
            background: #cb2d39;
            transform: translateY(-1px);
        }
        .btn-cancel {
            background: transparent;
            color: #64748B;
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        .secure-footer {
            margin-top: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            color: #94A3B8;
            font-weight: 600;
        }
        .spinner {
            border: 3px solid rgba(226, 55, 68, 0.2);
            border-top: 3px solid #E23744;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #successView, #failedView {
            display: none;
        }
    </style>
</head>
<body>

<div class="pay-card" id="mainCard">
    <div class="brand-badge">
        <span>⚡</span> Sip N Bite Razorpay Checkout
    </div>

    <div class="amount-box">
        <div class="amount-label">Amount Payable</div>
        <div class="amount-val">₹{{ number_format($amount, 2) }}</div>
        <div class="desc-text">{{ $description }}</div>
    </div>

    <div id="loaderBox">
        <div class="spinner"></div>
        <p style="font-size: 13px; color: #64748B; margin-bottom: 16px;">Launching Razorpay Gateway...</p>
    </div>

    <button id="rzp-button1" class="btn-pay" onclick="openRazorpay()">
        Pay ₹{{ number_format($amount, 2) }} Now
    </button>
    <button class="btn-cancel" onclick="cancelPayment()">Cancel & Return</button>

    <div class="secure-footer">
        <span>🔒</span> Secured by Razorpay 256-bit Encryption
    </div>
</div>

<div class="pay-card" id="successView">
    <div style="font-size: 54px; color: #16A34A; margin-bottom: 12px;">✓</div>
    <h3 style="color: #16A34A; font-weight: 800; margin-bottom: 8px;">Payment Successful!</h3>
    <p style="color: #475569; font-size: 14px; margin-bottom: 16px;" id="successMsg">Processing your transaction...</p>
</div>

<div class="pay-card" id="failedView">
    <div style="font-size: 54px; color: #DC2626; margin-bottom: 12px;">✕</div>
    <h3 style="color: #DC2626; font-weight: 800; margin-bottom: 8px;">Payment Failed</h3>
    <p style="color: #475569; font-size: 14px; margin-bottom: 20px;" id="failedMsg">Payment could not be completed.</p>
    <button class="btn-pay" onclick="retryPayment()">Try Again</button>
    <button class="btn-cancel" onclick="cancelPayment()">Cancel</button>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $razorpayKey }}",
        "amount": Math.round({{ $amount }} * 100),
        "currency": "INR",
        "name": "{{ $name }}",
        "description": "{{ $description }}",
        "image": "https://cdn-icons-png.flaticon.com/512/3170/3170733.png",
        "handler": function (response) {
            document.getElementById('mainCard').style.display = 'none';
            document.getElementById('successView').style.display = 'block';
            document.getElementById('successMsg').innerText = "Payment ID: " + response.razorpay_payment_id;

            var msg = {
                status: 'success',
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_order_id: response.razorpay_order_id || '',
                razorpay_signature: response.razorpay_signature || '',
                type: "{{ $type }}",
                order_id: "{{ $orderId }}",
                amount: {{ $amount }}
            };

            if (window.ReactNativeWebView) {
                window.ReactNativeWebView.postMessage(JSON.stringify(msg));
            }
        },
        "modal": {
            "ondismiss": function() {
                var msg = {
                    status: 'cancelled',
                    message: 'Payment dismissed by customer.'
                };
                if (window.ReactNativeWebView) {
                    window.ReactNativeWebView.postMessage(JSON.stringify(msg));
                }
            }
        },
        "prefill": {
            "name": "{{ $userName }}",
            "email": "{{ $userEmail }}",
            "contact": "{{ $userPhone }}"
        },
        "theme": {
            "color": "#E23744"
        }
    };

    var rzp1 = null;

    function openRazorpay() {
        try {
            if (!rzp1) {
                rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (response) {
                    document.getElementById('mainCard').style.display = 'none';
                    document.getElementById('failedView').style.display = 'block';
                    document.getElementById('failedMsg').innerText = response.error.description || 'Payment Failed';

                    var msg = {
                        status: 'failed',
                        message: response.error.description || 'Payment Failed'
                    };
                    if (window.ReactNativeWebView) {
                        window.ReactNativeWebView.postMessage(JSON.stringify(msg));
                    }
                });
            }
            rzp1.open();
        } catch(e) {
            console.error(e);
        }
    }

    function retryPayment() {
        document.getElementById('failedView').style.display = 'none';
        document.getElementById('mainCard').style.display = 'block';
        openRazorpay();
    }

    function cancelPayment() {
        var msg = {
            status: 'cancelled',
            message: 'User cancelled payment.'
        };
        if (window.ReactNativeWebView) {
            window.ReactNativeWebView.postMessage(JSON.stringify(msg));
        } else {
            window.history.back();
        }
    }

    // Automatically trigger Razorpay on page load
    window.onload = function() {
        setTimeout(function() {
            var loader = document.getElementById('loaderBox');
            if (loader) loader.style.display = 'none';
            openRazorpay();
        }, 300);
    };
</script>
</body>
</html>
