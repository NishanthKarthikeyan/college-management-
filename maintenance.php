<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$conn->query("CREATE TABLE IF NOT EXISTS maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resource_name VARCHAR(100) NOT NULL,
    issue_description TEXT NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(50) DEFAULT 'Pending',
    maintenance_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM maintenance WHERE id = $id");
    header("Location: maintenance.php?msg=Maintenance+record+deleted+successfully");
    exit();
}

$error_msg = "";

if (isset($_POST['add_maintenance'])) {
    $resource_name = trim($_POST['resource_name'] ?? '');
    $issue_description = trim($_POST['issue_description'] ?? '');
    $cost = trim($_POST['cost'] ?? '0');
    $status = trim($_POST['status'] ?? 'Pending');
    $maintenance_date = trim($_POST['maintenance_date'] ?? '');

    if (!empty($resource_name) && !empty($issue_description) && !empty($maintenance_date)) {
        $stmt = $conn->prepare("INSERT INTO maintenance (resource_name, issue_description, cost, status, maintenance_date) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssdss", $resource_name, $issue_description, $cost, $status, $maintenance_date);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: maintenance.php?msg=Maintenance+record+added+successfully");
                exit();
            } else {
                $error_msg = "Database Error: " . $stmt->error;
            }
        }
    } else {
        $error_msg = "Please fill all required fields!";
    }
}

$maintenance_result = $conn->query("SELECT * FROM maintenance ORDER BY maintenance_date DESC");
$resources_result = $conn->query("SELECT * FROM resources");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | College RMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- DataTables & Export CSS -->
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
            <a href="bookings.php"><i class="bi bi-calendar2-check-fill me-3"></i> Bookings</a>
            <a href="maintenance.php" class="active"><i class="bi bi-wrench-adjustable-circle-fill me-3"></i> Maintenance</a>
            <a href="resource_requests.php"><i class="bi bi-inbox-fill me-3"></i> Resource Requests</a>
            <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
        </div>
    </div>
    <div class="main-content flex-grow-1">
        <h3 class="fw-bold mb-4">Manage Maintenance</h3>

        <?php if (!empty($error_msg)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                🎉 Maintenance record added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-box p-4">
                    <h5 class="fw-bold mb-3">Add Maintenance</h5>
                    <form method="POST">
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
                                    echo '<option value="Projector 2">Projector 2</option>';
                                    echo '<option value="Seminar Hall A AC">Seminar Hall A AC</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Issue Description</label>
                            <textarea name="issue_description" class="form-control" rows="3" placeholder="Describe the issue..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Cost (â‚¹)</label>
                            <input type="number" step="0.01" name="cost" class="form-control" placeholder="0.00" value="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Maintenance Date</label>
                            <input type="date" name="maintenance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <button type="submit" name="add_maintenance" class="btn btn-primary w-100 fw-medium">Save Maintenance</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card-box p-4">
                    <h5 class="fw-bold mb-3">Maintenance Records</h5>
                    <div class="table-responsive mt-3">
                        <table id="maintenanceTable" class="table align-middle table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Resource</th>
                                    <th>Issue & Date</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($maintenance_result && $maintenance_result->num_rows > 0): ?>
                                    <?php while($row = $maintenance_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td class="fw-medium"><?php echo htmlspecialchars($row['resource_name']); ?></td>
                                            <td>
                                                <span class="d-block text-dark"><?php echo htmlspecialchars($row['issue_description']); ?></span>
                                                <small class="text-muted"><?php echo htmlspecialchars($row['maintenance_date']); ?></small>
                                            </td>
                                            <td>â‚¹<?php echo number_format($row['cost'], 2); ?></td>
                                            <td>
                                                <?php 
                                                    $badgeBg = 'bg-warning text-dark';
                                                    if ($row['status'] == 'Completed') $badgeBg = 'bg-success';
                                                    else if ($row['status'] == 'In Progress') $badgeBg = 'bg-info text-dark';
                                                ?>
                                                <span class="badge <?php echo $badgeBg; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                            </td>
                                            <td class="text-end">
                                                <a href="edit_maintenance.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                                <a href="maintenance.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this record?')" title="Delete"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No maintenance records found.</td>
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
<!-- DataTables & Export JS -->
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
        $('#maintenanceTable').DataTable({
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





