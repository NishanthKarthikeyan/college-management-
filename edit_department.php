<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Get record
if (!isset($_GET['id'])) {
    header("Location: departments.php");
    exit();
}
$id = intval($_GET['id']);
$record = $conn->query("SELECT * FROM departments WHERE id = $id")->fetch_assoc();
if (!$record) {
    header("Location: departments.php?msg=Department+not+found");
    exit();
}

$error_msg = "";

// Handle Update
if (isset($_POST['update_dept'])) {
    $dept_name = trim($_POST['dept_name'] ?? '');
    if (!empty($dept_name)) {
        $stmt = $conn->prepare("UPDATE departments SET dept_name=? WHERE id=?");
        $stmt->bind_param("si", $dept_name, $id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: departments.php?msg=Department+updated+successfully");
            exit();
        } else {
            $error_msg = "Database Error: " . $stmt->error;
        }
    } else {
        $error_msg = "Department name cannot be empty!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department | College RMS</title>
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
        .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 0.2rem rgba(99,102,241,0.15); }
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
            <a href="departments.php" class="active"><i class="bi bi-diagram-3-fill me-3"></i> Departments</a>
            <a href="resources.php?type=Classroom"><i class="bi bi-door-open-fill me-3"></i> Classrooms</a>
            <a href="resources.php?type=Computer Lab"><i class="bi bi-laptop-fill me-3"></i> Computer Labs</a>
            <a href="resources.php?type=Meeting Room"><i class="bi bi-people-fill me-3"></i> Meeting Rooms</a>
            <a href="resources.php?type=Seminar Hall"><i class="bi bi-mic-fill me-3"></i> Seminar Hall</a>
            <a href="resources.php?type=Equipment"><i class="bi bi-tools me-3"></i> Equipment</a>
            <a href="bookings.php"><i class="bi bi-calendar2-check-fill me-3"></i> Bookings</a>
            <a href="maintenance.php"><i class="bi bi-wrench-adjustable-circle-fill me-3"></i> Maintenance</a>
            <a href="resource_requests.php"><i class="bi bi-inbox-fill me-3"></i> Resource Requests</a>
            <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
        </div>
    </div>

    <div class="main-content flex-grow-1">

        <div class="page-header">
            <a href="departments.php" class="back-btn"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3 class="fw-bold mb-0">Edit Department</h3>
                <small class="text-muted">Updating: <strong><?php echo htmlspecialchars($record['dept_name'] ?? $record['name'] ?? ''); ?></strong></small>
            </div>
        </div>

        <?php if (!empty($error_msg)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card-box p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-pencil-fill me-2 text-primary"></i>Update Department Name</h5>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-medium">Department Name</label>
                            <input
                                type="text"
                                name="dept_name"
                                class="form-control form-control-lg"
                                value="<?php echo htmlspecialchars($record['dept_name'] ?? $record['name'] ?? ''); ?>"
                                placeholder="e.g. Computer Science"
                                required
                                autofocus
                            >
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update_dept" class="btn btn-save flex-grow-1">
                                <i class="bi bi-check-circle-fill me-2"></i>Update Department
                            </button>
                            <a href="departments.php" class="btn btn-outline-secondary px-4">
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


