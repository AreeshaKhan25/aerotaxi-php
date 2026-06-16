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
                        <td style="background-color:#111827;padding:24px 40px;text-align:center;">
                            <h1 style="margin:0;font-size:20px;color:#FACC15;font-weight:bold;">AeroTAXI - Admin</h1>
                        </td>
                    </tr>

                    <!-- Alert Banner -->
                    <tr>
                        <td style="padding:30px 40px 20px;">
                            <div style="background-color:#fef9c3;border:2px solid #FACC15;border-radius:12px;padding:16px 20px;">
                                <h2 style="margin:0 0 4px;font-size:18px;color:#111827;">&#128230; New Booking Received</h2>
                                <p style="margin:0;font-size:14px;color:#6b7280;">A new transfer has been booked and paid for.</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Reference -->
                    <tr>
                        <td style="padding:10px 40px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="font-size:12px;color:#9ca3af;">REFERENCE</td>
                                    <td align="right" style="font-size:12px;color:#9ca3af;">BOOKED AT</td>
                                </tr>
                                <tr>
                                    <td style="font-size:18px;color:#111827;font-weight:bold;letter-spacing:1px;"><?= e($booking->reference) ?></td>
                                    <td align="right" style="font-size:14px;color:#111827;"><?= e(format_date($booking->created_at, 'j M Y, H:i')) ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Journey Details -->
                    <tr>
                        <td style="padding:0 40px 20px;">
                            <h3 style="margin:0 0 12px;font-size:14px;color:#6b7280;text-transform:uppercase;letter-spacing:1px;">Journey Details</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                                <tr>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;background-color:#f9fafb;width:120px;">
                                        <span style="font-size:12px;color:#6b7280;font-weight:600;">From</span>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:14px;color:#111827;"><?= e($booking->from_location) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;background-color:#f9fafb;">
                                        <span style="font-size:12px;color:#6b7280;font-weight:600;">To</span>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:14px;color:#111827;"><?= e($booking->to_location) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;background-color:#f9fafb;">
                                        <span style="font-size:12px;color:#6b7280;font-weight:600;">Date & Time</span>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:14px;color:#111827;">
                                            <?= e(format_date($booking->depart_date, 'l, j F Y')) ?>
                                            <?php if(!empty($booking->depart_time)): ?> at <?= e($booking->depart_time) ?><?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;background-color:#f9fafb;">
                                        <span style="font-size:12px;color:#6b7280;font-weight:600;">Vehicle</span>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:14px;color:#111827;">
                                            <?= e($booking->vehicle->name ?? 'N/A') ?>
                                            <?php if($booking->vehicle && !empty($booking->vehicle->car_model)): ?>
                                                (<?= e($booking->vehicle->car_model) ?>)
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Customer Details -->
                    <tr>
                        <td style="padding:0 40px 20px;">
                            <h3 style="margin:0 0 12px;font-size:14px;color:#6b7280;text-transform:uppercase;letter-spacing:1px;">Customer Details</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                                <tr>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;background-color:#f9fafb;width:120px;">
                                        <span style="font-size:12px;color:#6b7280;font-weight:600;">Name</span>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                                        <span style="font-size:14px;color:#111827;"><?= e($booking->passenger_name) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;background-color:#f9fafb;">
                                        <span style="font-size:12px;color:#6b7280;font-weight:600;">Email</span>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                                        <a href="mailto:<?= e($booking->email) ?>" style="font-size:14px;color:#0C6291;"><?= e($booking->email) ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px;background-color:#f9fafb;">
                                        <span style="font-size:12px;color:#6b7280;font-weight:600;">Phone</span>
                                    </td>
                                    <td style="padding:12px 16px;">
                                        <a href="tel:<?= e($booking->phone) ?>" style="font-size:14px;color:#0C6291;"><?= e($booking->phone) ?></a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Payment -->
                    <tr>
                        <td style="padding:0 40px 30px;">
                            <div style="background-color:#f0fdf4;border-radius:12px;padding:16px 20px;text-align:center;">
                                <span style="font-size:12px;color:#6b7280;text-transform:uppercase;">Amount Charged</span><br>
                                <span style="font-size:28px;font-weight:bold;color:#111827;">&pound;<?= e(number_format($booking->total_price, 2)) ?></span><br>
                                <span style="font-size:12px;color:#16a34a;">&#10003; Payment confirmed</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb;padding:20px 40px;text-align:center;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;font-size:11px;color:#9ca3af;">
                                This is an automated notification from AeroTAXI.<br>
                                &copy; <?= date('Y') ?> AeroTAXI LLC.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
