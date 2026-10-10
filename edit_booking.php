<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Get record
if (!isset($_GET['id'])) {
    header("Location: bookings.php");
    exit();
}
$id = intval($_GET['id']);
$record = $conn->query("SELECT * FROM bookings WHERE id = $id")->fetch_assoc();
if (!$record) {
    header("Location: bookings.php?msg=Booking+not+found");
    exit();
}

$error_msg = "";

// Handle Update
if (isset($_POST['update_booking'])) {
    $department = trim($_POST['department'] ?? '');
    $resource_name = trim($_POST['resource_name'] ?? '');
    $booking_date = trim($_POST['booking_date'] ?? '');
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time = trim($_POST['end_time'] ?? '');
    $booked_by = trim($_POST['booked_by'] ?? '');

    if ($department != "" && $resource_name != "" && $booking_date != "" && $start_time != "" && $end_time != "" && $booked_by != "") {
        
        // Check if resource is already booked for that date and overlapping time (excluding current booking)
        $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE resource_name = ? AND booking_date = ? AND ((start_time <= ? AND end_time >= ?) OR (start_time <= ? AND end_time >= ?)) AND id != ?");
        $check_stmt->bind_param("ssssssi", $resource_name, $booking_date, $start_time, $start_time, $end_time, $end_time, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_msg = "âš ï¸ This resource is already booked for the selected date and time slot!";
        } else {
            $stmt = $conn->prepare("UPDATE bookings SET department=?, resource_name=?, booking_date=?, start_time=?, end_time=?, booked_by=? WHERE id=?");
            if ($stmt) {
                $stmt->bind_param("ssssssi", $department, $resource_name, $booking_date, $start_time, $end_time, $booked_by, $id);
                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: bookings.php?msg=Booking+updated+successfully");
                    exit();
                } else {
                    $error_msg = "Database Error: " . $stmt->error;
                }
            }
        }
        $check_stmt->close();
    } else {
        $error_msg = "All fields are required!";
    }
}

$departments_result = $conn->query("SELECT * FROM departments");
$resources_result = $conn->query("SELECT * FROM resources");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking | College RMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f3f8; color: #1e293b; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #fff; width: 250px; position: fixed; }
        .sidebar .brand { font-size: 20px; font-weight: 700; padding: 22px 20px; color: #6366f1; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 20px; display: flex; align-items: center; font-size: 14px; font-weight: 500; border-radius: 8px; margin: 5px 12px; transition: all 0.3s; }
        .sidebar a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }
        .sidebar a.active { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #fff; }
        .main-content { margin-left: 250px; padding: 35px; }
        .card-box { border: none; border-radius: 16px; background: #fff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
        .form-control:focus, .form-select:focus { border-color: #6366f1; box-shadow: 0 0 0 0.2rem rgba(99,102,241,0.15); }
        .btn-save { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; border: none; border-radius: 10px; padding: 12px 28px; font-weight: 600; transition: all 0.3s; }
        .btn-save:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(99,102,241,0.35); color: white; }
        .page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; }
        .back-btn { width: 40px; height: 40px; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b; text-decoration: none; transition: all 0.2s; flex-shrink: 0; }
        .back-btn:hover { background: #f1f5f9; color: #1e293b; }
    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand"><i class="bi bi-building-gear me-2"></i> College RMS</div>
        <div class="mt-3">
            <a href="dashboard.php"><i class="bi bi-grid-1x2-fill me-3"></i> Dashboard</a>
            <a href="departments.php"><i class="bi bi-diagram-3-fill me-3"></i> Departments</a>
            <a href="resources.php?type=Classroom"><i class="bi bi-door-open-fill me-3"></i> Classrooms</a>
            <a href="resources.php?type=Computer Lab"><i class="bi bi-laptop-fill me-3"></i> Computer Labs</a>
            <a href="resources.php?type=Meeting Room"><i class="bi bi-people-fill me-3"></i> Meeting Rooms</a>
            <a href="resources.php?type=Seminar Hall"><i class="bi bi-mic-fill me-3"></i> Seminar Hall</a>
            <a href="resources.php?type=Equipment"><i class="bi bi-tools me-3"></i> Equipment</a>
            <a href="bookings.php" class="active"><i class="bi bi-calendar2-check-fill me-3"></i> Bookings</a>
            <a href="maintenance.php"><i class="bi bi-wrench-adjustable-circle-fill me-3"></i> Maintenance</a>
            <a href="resource_requests.php"><i class="bi bi-inbox-fill me-3"></i> Resource Requests</a>
            <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
        </div>
    </div>

    <div class="main-content flex-grow-1">

        <div class="page-header">
            <a href="bookings.php" class="back-btn"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3 class="fw-bold mb-0">Edit Booking</h3>
                <small class="text-muted">Updating booking ID: <strong>#<?php echo htmlspecialchars($record['id']); ?></strong></small>
            </div>
        </div>

        <?php if (!empty($error_msg)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card-box p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-pencil-fill me-2 text-primary"></i>Update Booking Details</h5>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Department</label>
                            <select name="department" class="form-select" required>
                                <option value="">Select Department</option>
                                <?php 
                                $departments = [
                                    'placement',
                                    'management',
                                    'Commerce',
                                    'Computer Science',
                                    'Physics',
                                    'Mathematics',
                                    'English',
                                    'Tamil'
                                ];
                                foreach ($departments as $dName) {
                                    $selected = ($dName == $record['department']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($dName) . '" ' . $selected . '>' . htmlspecialchars($dName) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Resource Name</label>
                            <select name="resource_name" class="form-select" required>
                                <option value="">Select Resource</option>
                                <?php 
                                $has_res = false;
                                if ($resources_result && $resources_result->num_rows > 0) {
                                    while($res = $resources_result->fetch_assoc()) {
                                        $rName = $res['name'] ?? ($res['resource_name'] ?? '');
                                        if(!empty($rName)) {
                                            $has_res = true;
                                            $selected = ($rName == $record['resource_name']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($rName) . '" ' . $selected . '>' . htmlspecialchars($rName) . '</option>';
                                        }
                                    }
                                }
                                if (!$has_res) {
                                    $default_res = ['Lab 1 - Computer Centre', 'Seminar Hall A', 'Classroom 101', 'Smart Board Projector 1', 'Auditorium'];
                                    foreach($default_res as $rName) {
                                        $selected = ($rName == $record['resource_name']) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($rName) . '" ' . $selected . '>' . htmlspecialchars($rName) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Booking Date</label>
                            <input type="date" name="booking_date" class="form-control" value="<?php echo htmlspecialchars($record['booking_date']); ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-medium">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($record['start_time']); ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-medium">End Time</label>
                                <input type="time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($record['end_time']); ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-medium">Booked By (Name)</label>
                            <input type="text" name="booked_by" class="form-control" value="<?php echo htmlspecialchars($record['booked_by']); ?>" placeholder="Enter your name" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update_booking" class="btn btn-save flex-grow-1">
                                <i class="bi bi-check-circle-fill me-2"></i>Update Booking
                            </button>
                            <a href="bookings.php" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-lg me-1"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>



