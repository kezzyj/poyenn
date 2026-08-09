<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#f4f4f7; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7; padding: 24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:8px; overflow:hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#0D1F4E; padding: 24px 32px;">
                            <span style="color:#ffffff; font-size:24px; font-weight:bold;">Poyenn</span>
                            <span style="color:#00AEEF; font-size:13px; display:block; margin-top:2px;">Quality Electronics, Delivered.</span>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="margin:0 0 8px; font-size:20px; color:#1a1a1a;">{{ $heading }}</h1>
                            <p style="margin:0 0 20px; font-size:15px; color:#555; line-height:1.6;">{{ $message }}</p>

                            {{-- Order summary box --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fb; border-radius:8px; margin: 16px 0;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin:0 0 4px; font-size:12px; color:#888; text-transform:uppercase;">Order Number</p>
                                        <p style="margin:0 0 12px; font-size:16px; font-weight:bold; color:#1a1a1a;">{{ $order->order_number }}</p>

                                        <p style="margin:0 0 4px; font-size:12px; color:#888; text-transform:uppercase;">Status</p>
                                        <p style="margin:0 0 12px; font-size:15px; font-weight:bold; color:#0D1F4E;">{{ $order->status_label }}</p>

                                        <p style="margin:0 0 4px; font-size:12px; color:#888; text-transform:uppercase;">Total</p>
                                        <p style="margin:0; font-size:18px; font-weight:bold; color:#1a1a1a;">₦{{ number_format($order->total_amount, 2) }}</p>
                                    </td>
                                </tr>
                            </table>

                             {{-- Items --}}
                            <p style="margin: 24px 0 8px; font-size:13px; color:#888; text-transform:uppercase; font-weight:bold;">Items Ordered</p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 16px;">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td width="60" style="padding: 10px 0; border-bottom:1px solid #eee; vertical-align:top;">
                                            @if($item->product_image)
                                                <img src="{{ rtrim(config('app.url'), '/') }}/storage/{{ $item->product_image }}"
                                                     width="48" height="48"
                                                     alt="{{ $item->product_name }}"
                                                     style="width:48px; height:48px; border-radius:6px; object-fit:cover; display:block; background-color:#f0f0f0;">
                                            @else
                                                <div style="width:48px; height:48px; border-radius:6px; background-color:#f0f0f0;"></div>
                                            @endif
                                        </td>
                                        <td style="padding: 10px 12px; border-bottom:1px solid #eee; font-size:14px; color:#555; vertical-align:top;">
                                            {{ $item->product_name }}<br>
                                            <span style="color:#999; font-size:13px;">Qty: {{ $item->quantity }} × ₦{{ number_format($item->unit_price, 2) }}</span>
                                        </td>
                                        <td style="padding: 10px 0; border-bottom:1px solid #eee; font-size:14px; color:#1a1a1a; text-align:right; font-weight:bold; vertical-align:top; white-space:nowrap;">
                                            ₦{{ number_format($item->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            {{-- Track button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
                                <tr>
                                    <td style="background-color:#00AEEF; border-radius:6px;">
                                        <a href="{{ $trackUrl }}" style="display:inline-block; padding: 12px 28px; color:#ffffff; font-size:15px; font-weight:bold; text-decoration:none;">
                                            Track Your Order
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0; font-size:13px; color:#888; line-height:1.6;">
                                Delivering to: {{ $order->delivery_recipient_name }}, {{ $order->delivery_city }}, {{ $order->delivery_state }}
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f8f9fb; padding: 24px 32px; text-align:center;">
                            <p style="margin:0 0 4px; font-size:13px; color:#888;">Thank you for shopping with Poyenn.</p>
                            <p style="margin:0; font-size:12px; color:#aaa;">© {{ date('Y') }} Poyenn. All rights reserved.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>