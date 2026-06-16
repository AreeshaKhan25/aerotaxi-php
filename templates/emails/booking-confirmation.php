<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#FACC15;padding:30px 40px;text-align:center;">
                            <h1 style="margin:0;font-size:24px;color:#111827;font-weight:bold;">AeroTAXI</h1>
                        </td>
                    </tr>

                    <!-- Confirmation Banner -->
                    <tr>
                        <td style="padding:40px 40px 20px;text-align:center;">
                            <div style="width:60px;height:60px;border-radius:50%;background-color:#dcfce7;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                                <span style="font-size:28px;color:#16a34a;">&#10003;</span>
                            </div>
                            <h2 style="margin:0 0 8px;font-size:22px;color:#111827;">Booking Confirmed!</h2>
                            <p style="margin:0;color:#6b7280;font-size:14px;">Your airport transfer has been booked successfully.</p>
                        </td>
                    </tr>

                    <!-- Reference -->
                    <tr>
                        <td style="padding:10px 40px 30px;text-align:center;">
                            <div style="display:inline-block;background-color:#fef9c3;border:2px solid #FACC15;border-radius:12px;padding:12px 24px;">
                                <span style="font-size:12px;color:#6b7280;">Booking Reference</span><br>
                                <span style="font-size:20px;font-weight:bold;color:#111827;letter-spacing:2px;"><?= e($booking->reference) ?></span>
                            </div>
                        </td>
                    </tr>

                    <!-- Booking Details -->
                    <tr>
                        <td style="padding:0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:12px;color:#9ca3af;text-transform:uppercase;">From</span><br>
                                        <span style="font-size:14px;color:#111827;font-weight:600;"><?= e($booking->from_location) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:12px;color:#9ca3af;text-transform:uppercase;">To</span><br>
                                        <span style="font-size:14px;color:#111827;font-weight:600;"><?= e($booking->to_location) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:12px;color:#9ca3af;text-transform:uppercase;">Date & Time</span><br>
                                        <span style="font-size:14px;color:#111827;font-weight:600;">
                                            <?= e(format_date($booking->depart_date, 'l, j F Y')) ?>
                                            <?php if(!empty($booking->depart_time)): ?> at <?= e($booking->depart_time) ?><?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:12px;color:#9ca3af;text-transform:uppercase;">Vehicle</span><br>
                                        <span style="font-size:14px;color:#111827;font-weight:600;">
                                            <?= e($booking->vehicle->name ?? 'N/A') ?>
                                            <?php if($booking->vehicle && !empty($booking->vehicle->car_model)): ?>
                                                <span style="color:#9ca3af;font-weight:400;">(<?= e($booking->vehicle->car_model) ?>)</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:12px;color:#9ca3af;text-transform:uppercase;">Passenger</span><br>
                                        <span style="font-size:14px;color:#111827;font-weight:600;"><?= e($booking->passenger_name) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <span style="font-size:12px;color:#9ca3af;text-transform:uppercase;">Total Paid</span><br>
                                        <span style="font-size:20px;color:#111827;font-weight:bold;">&pound;<?= e(number_format($booking->total_price, 2)) ?></span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Features -->
                    <tr>
                        <td style="padding:0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;border-radius:12px;padding:16px;">
                                <tr>
                                    <td style="padding:16px 20px;font-size:13px;color:#15803d;">
                                        &#10003; Meet & Greet included &nbsp;&nbsp;
                                        &#10003; Free flight tracking &nbsp;&nbsp;
                                        &#10003; Free cancellation (24h) &nbsp;&nbsp;
                                        &#10003; 24/7 support
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Important Info -->
                    <tr>
                        <td style="padding:0 40px 30px;">
                            <h3 style="margin:0 0 12px;font-size:16px;color:#111827;">Important Information</h3>
                            <ul style="margin:0;padding-left:20px;color:#6b7280;font-size:13px;line-height:22px;">
                                <li>Your driver will track your flight automatically for delays.</li>
                                <li>Please arrive at the pickup point on time.</li>
                                <li>For airport pickups, your driver will be waiting in the arrivals hall with a name board.</li>
                                <li>To amend or cancel, visit our website or email supportaerotaxi@gmail.com.</li>
                                <li>Free cancellation up to 24 hours before your scheduled pickup.</li>
                            </ul>
                        </td>
                    </tr>

                    <!-- CTA -->
                    <tr>
                        <td style="padding:0 40px 30px;text-align:center;">
                            <a href="<?= url('/help') ?>" style="display:inline-block;background-color:#FACC15;color:#111827;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:12px;font-size:14px;">
                                Manage My Booking
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 8px;font-size:13px;color:#6b7280;">
                                Need help? Contact us at
                                <a href="mailto:supportaerotaxi@gmail.com" style="color:#0C6291;text-decoration:none;">supportaerotaxi@gmail.com</a>
                            </p>
                            <p style="margin:0;font-size:11px;color:#9ca3af;">
                                &copy; <?= date('Y') ?> AeroTAXI LLC. All rights reserved.<br>
                                1111B S Governors Ave STE 26937, Dover, DE 19904, US
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
