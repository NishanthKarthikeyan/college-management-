<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized access.");
}
include 'db.php';

if (!isset($_GET['id'])) {
    die("Invalid booking ID.");
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found.");
}
$booking = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Slip - <?php echo $id; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f3f8; padding: 20px; }
        .slip-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h2 { font-weight: 700; color: #1e293b; margin: 0; }
        .header p { color: #64748b; margin: 5px 0 0; }
        
        .slip-table th { width: 35%; background: #f8fafc; color: #475569; font-weight: 600; padding: 12px 15px; border: 1px solid #e2e8f0; }
        .slip-table td { padding: 12px 15px; border: 1px solid #e2e8f0; color: #1e293b; font-weight: 500; }
        
        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #1e293b;
            margin-bottom: 10px;
            height: 40px;
        }
        .signature-box p { margin: 0; color: #64748b; font-size: 14px; }
        
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(79,70,229,0.3);
        }
        
        @media print {
            body { background: #fff; padding: 0; }
            .slip-container { box-shadow: none; padding: 0; border: none; }
            .print-btn { display: none; }
            .footer { page-break-inside: avoid; }
        }
    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>
<body>

<div class="slip-container" id="printableArea">
    <div class="header">
        <h2><i class="bi bi-building-gear text-primary"></i> COLLEGE RMS</h2>
        <p>Official Resource Booking Confirmation Slip</p>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <div>
            <span class="text-muted small d-block">Booking ID:</span>
            <span class="fw-bold fs-5">#<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></span>
        </div>
        <div class="text-end">
            <span class="text-muted small d-block">Generated On:</span>
            <span class="fw-bold"><?php echo date('d M Y, h:i A'); ?></span>
        </div>
    </div>
    
    <table class="table slip-table mb-4">
        <tbody>
            <tr>
                <th>Resource Name</th>
                <td><?php echo htmlspecialchars($booking['resource_name']); ?></td>
            </tr>
            <tr>
                <th>Department</th>
                <td><?php echo htmlspecialchars($booking['department']); ?></td>
            </tr>
            <tr>
                <th>Booked By (Student/Staff)</th>
                <td><?php echo htmlspecialchars($booking['booked_by']); ?></td>
            </tr>
            <tr>
                <th>Booking Date</th>
                <td><?php echo date('l, d F Y', strtotime($booking['booking_date'])); ?></td>
            </tr>
            <tr>
                <th>Time Slot</th>
                <td>
                    <span class="badge bg-primary fs-6">
                        <i class="bi bi-clock me-1"></i>
                        <?php echo date('h:i A', strtotime($booking['start_time'])); ?> - 
                        <?php echo date('h:i A', strtotime($booking['end_time'])); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> APPROVED</span></td>
            </tr>
        </tbody>
    </table>
    
    <div class="alert alert-light border text-center small text-muted">
        This is a system-generated document. Please present this slip to the lab assistant or security personnel to access the booked resource.
    </div>
    
    <div class="footer">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p>Applicant Signature</p>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <!-- Can place a digital stamp here if needed -->
                <strong style="font-size:18px; color:#4f46e5; font-family:cursive;">Approved</strong>
            </div>
            <p>Admin / Authority Signature</p>
        </div>
    </div>
</div>

<button class="btn btn-primary print-btn" onclick="window.print()">
    <i class="bi bi-printer-fill me-2"></i> Print / Save as PDF
</button>

<script>
    // Automatically trigger print dialog when page loads
    window.onload = function() {
        setTimeout(() => {
            window.print();
        }, 500);
    };
</script>

    <script src="mobile_view.js"></script>
</body>
</html>

