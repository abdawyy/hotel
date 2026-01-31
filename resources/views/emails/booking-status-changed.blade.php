<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Status Update</title>
    <style>
        body {
            background: #f4f6fb;
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.10);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            padding: 32px 24px 20px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 8px;
        }
        .content {
            padding: 32px 24px 24px 24px;
            color: #222;
        }
        .content h2 {
            margin-top: 0;
            font-size: 1.25rem;
            color: #2563eb;
        }
        .details {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 18px 16px;
            margin: 24px 0 16px 0;
            font-size: 1rem;
        }
        .details strong {
            color: #3b82f6;
        }
        .footer {
            background: #f4f6fb;
            color: #888;
            text-align: center;
            font-size: 0.95rem;
            padding: 18px 0 10px 0;
            border-top: 1px solid #e5e7eb;
        }
        .btn {
            display: inline-block;
            background: #3b82f6;
            color: #fff !important;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 18px;
            box-shadow: 0 2px 8px rgba(59,130,246,0.10);
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">🏨</div>
            <h1>Booking Update</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $booking->guest_name }},</h2>
            <p style="margin-bottom: 18px;">
                @if($messageText)
                    {{ $messageText }}
                @else
                    The status of your booking <strong>#{{ $booking->booking_number }}</strong> has been updated to <strong>{{ ucfirst($booking->status) }}</strong>.
                @endif
            </p>
            <div class="details">
                <div><strong>Booking #:</strong> {{ $booking->booking_number }}</div>
                <div><strong>Check-in:</strong> {{ $booking->check_in_date->format('F d, Y') }}</div>
                <div><strong>Check-out:</strong> {{ $booking->check_out_date->format('F d, Y') }}</div>
                <div><strong>Guests:</strong> {{ $booking->adults }} adults, {{ $booking->children }} children</div>
                <div><strong>Status:</strong> {{ ucfirst($booking->status) }}</div>
            </div>
            <p style="margin-bottom: 0;">If you have any questions, please contact us.<br>Thank you for choosing our hotel!</p>
            <a href="{{ url('/') }}" class="btn">Visit Our Website</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Hotel') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
