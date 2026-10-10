<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$conn->query("CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department VARCHAR(100) NOT NULL,
    resource_name VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    booked_by VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS booked_by VARCHAR(100) NOT NULL");
$conn->query("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS start_time TIME NOT NULL");
$conn->query("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS end_time TIME NOT NULL");

$error_msg = "";
$success_msg = "";

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM bookings WHERE id = $id");
    header("Location: bookings.php?msg=Booking+deleted+successfully");
    exit();
}

if (isset($_POST['add_booking'])) {
    $department = trim($_POST['department'] ?? '');
    $resource_name = trim($_POST['resource_name'] ?? '');
    $booking_date = trim($_POST['booking_date'] ?? '');
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time = trim($_POST['end_time'] ?? '');
    $booked_by = trim($_POST['booked_by'] ?? '');

    if ($department != "" && $resource_name != "" && $booking_date != "" && $start_time != "" && $end_time != "" && $booked_by != "") {
        
        // Check if resource is already booked for that date and overlapping time
        $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE resource_name = ? AND booking_date = ? AND ((start_time <= ? AND end_time >= ?) OR (start_time <= ? AND end_time >= ?))");
        $check_stmt->bind_param("ssssss", $resource_name, $booking_date, $start_time, $start_time, $end_time, $end_time);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_msg = "Ã¢Å¡Â Ã¯Â¸Â This resource is already booked for the selected date and time slot!";
        } else {
            $stmt = $conn->prepare("INSERT INTO bookings (department, resource_name, booking_date, start_time, end_time, booked_by) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssssss", $department, $resource_name, $booking_date, $start_time, $end_time, $booked_by);
                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: bookings.php?success=1");
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

$bookings_result = $conn->query("SELECT * FROM bookings ORDER BY booking_date DESC");
$departments_result = $conn->query("SELECT * FROM departments");
$resources_result = $conn->query("SELECT * FROM resources");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings | College RMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- DataTables & Export Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
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
    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>
<body>
<div class="d-flex">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Manage Bookings</h3>
            <a href="calendar.php" class="btn btn-primary shadow-sm"><i class="bi bi-calendar-check me-2"></i>View Calendar</a>
        </div>

        <?php if (!empty($error_msg)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="init">
                <?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <!-- Error / Already Booked Warning Sound using Web Audio API -->
            <script>
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                function playWarningSound() {
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(300, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(120, audioCtx.currentTime + 0.3);
                    gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.3);
                }
                playWarningSound();
            </script>
        <?php } ?>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="init">
                Ã°Å¸Å½â€° Resource booked successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                const audioCtx2 = new (window.AudioContext || window.webkitAudioContext)();
                function playSuccessSound() {
                    const osc = audioCtx2.createOscillator();
                    const gain = audioCtx2.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587.33, audioCtx2.currentTime); // D5
                    osc.frequency.setValueAtTime(880, audioCtx2.currentTime + 0.15); // A5
                    gain.gain.setValueAtTime(0.2, audioCtx2.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioCtx2.currentTime + 0.4);
                    osc.connect(gain);
                    gain.connect(audioCtx2.destination);
                    osc.start();
                    osc.stop(audioCtx2.currentTime + 0.4);
                }
                playSuccessSound();
            </script>
        <?php } ?>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-box p-4">
                    <h5 class="fw-bold mb-3">New Booking</h5>
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
                                    echo '<option value="' . htmlspecialchars($dName) . '">' . htmlspecialchars($dName) . '</option>';
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
                                            echo '<option value="' . htmlspecialchars($rName) . '">' . htmlspecialchars($rName) . '</option>';
                                        }
                                    }
                                }
                                if (!$has_res) {
                                    echo '<option value="Lab 1 - Computer Centre">Lab 1 - Computer Centre</option>';
                                    echo '<option value="Seminar Hall A">Seminar Hall A</option>';
                                    echo '<option value="Classroom 101">Classroom 101</option>';
                                    echo '<option value="Smart Board Projector 1">Smart Board Projector 1</option>';
                                    echo '<option value="Auditorium">Auditorium</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Booking Date</label>
                            <input type="date" name="booking_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-medium">Start Time</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-medium">End Time</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Booked By (Name)</label>
                            <input type="text" name="booked_by" class="form-control" placeholder="Enter your name" required>
                        </div>
                        <button type="submit" name="add_booking" class="btn btn-primary w-100 fw-medium">Add Booking Details</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card-box p-4">
                    <h5 class="fw-bold mb-3">Booking List</h5>
                    <div class="table-responsive mt-3">
                        <table id="bookingsTable" class="table align-middle table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Department</th>
                                    <th>Resource</th>
                                    <th>Date & Time</th>
                                    <th>Booked By</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($bookings_result && $bookings_result->num_rows > 0): ?>
                                    <?php while($row = $bookings_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td class="fw-medium"><?php echo htmlspecialchars($row['department']); ?></td>
                                            <td><?php echo htmlspecialchars($row['resource_name']); ?></td>
                                            <td>
                                                <small class="d-block text-muted"><?php echo htmlspecialchars($row['booking_date']); ?></small>
                                                <span><?php echo htmlspecialchars($row['start_time']) . ' - ' . htmlspecialchars($row['end_time']); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['booked_by']); ?></td>
                                            <td class="text-end">
                                                <a href="booking_slip.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-success me-1" title="Print Slip"><i class="bi bi-printer-fill"></i></a>
                                                <a href="edit_booking.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                                <a href="bookings.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this booking?')" title="Delete"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No bookings found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables & Buttons JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#bookingsTable').DataTable({
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: { columns: [0, 1, 2, 3, 4] }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: { columns: [0, 1, 2, 3, 4] }
                }
            ],
            pageLength: 10,
            order: [[0, 'desc']]
        });
    });
</script>
<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>






