<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) OR $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS resource_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    resource_name VARCHAR(100) NOT NULL,
    quantity INT DEFAULT 1,
    purpose TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$message = "";

// Handle Form Submission
if (isset($_POST['submit_req'])) {
    $dept = trim($_POST['department_name'] ?? '');
    $res = trim($_POST['resource_name'] ?? '');
    $qty = intval($_POST['quantity'] ?? 1);
    $pur = trim($_POST['purpose'] ?? '');

    if (!empty($dept) AND !empty($res) AND !empty($pur)) {
        $stmt = $conn->prepare("INSERT INTO resource_requests (department_name, resource_name, quantity, purpose, status) VALUES (?, ?, ?, ?, 'Pending')");
        if ($stmt) {
            $stmt->bind_param("ssis", $dept, $res, $qty, $pur);
            $stmt->execute();
            $stmt->close();
            $message = "Success";
        }
    }
}

// Handle Approve / Reject Actions
if (isset($_GET['action']) AND isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = ($action == 'approve') ? 'Approved' : 'Rejected';
    
    $stmt = $conn->prepare("UPDATE resource_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: resource_requests.php?msg=Status+updated+successfully");
    exit();
}

// Handle Delete Action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM resource_requests WHERE id = $id");
    header("Location: resource_requests.php?msg=Record+deleted+successfully");
    exit();
}

$result = $conn->query("SELECT * FROM resource_requests ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Resource Requests Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- DataTables & Export CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f3f8; color: #1e293b; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #fff; width: 250px; position: fixed; z-index: 1000; }
        .sidebar .brand { font-size: 20px; font-weight: 700; padding: 22px 20px; color: #6366f1; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 20px; display: flex; align-items: center; font-size: 14px; font-weight: 500; border-radius: 8px; margin: 5px 12px; transition: all 0.3s; }
        .sidebar a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }
        .sidebar a.active { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #fff; }
        .main-content { margin-left: 250px; padding: 35px; }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
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
                <a href="maintenance.php"><i class="bi bi-wrench-adjustable-circle-fill me-3"></i> Maintenance</a>
                <a href="resource_requests.php" class="active"><i class="bi bi-inbox-fill me-3"></i> Resource Requests</a>
            <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
            </div>
        </div>
        <div class="main-content flex-grow-1">
            <div class="container-fluid">
                <h2 class="mb-4">Resource Requests Management</h2>
        
        <?php if($message == "Success"): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Request added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h4>Add Request</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Department</label>
                            <select name="department_name" class="form-control" required>
                                <option value="">Select</option>
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
                                foreach ($departments as $dept) {
                                    echo '<option value="' . htmlspecialchars($dept) . '">' . htmlspecialchars($dept) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Resource Name</label>
                            <input type="text" name="resource_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label>Purpose</label>
                            <textarea name="purpose" class="form-control" required></textarea>
                        </div>
                        <button type="submit" name="submit_req" class="btn btn-primary w-100">Submit</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card p-3 shadow-sm">
                    <h4>All Requests</h4>
                    <div class="table-responsive mt-3">
                        <table id="requestsTable" class="table align-middle table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Dept</th>
                                    <th>Resource</th>
                                    <th>Qty/Purpose</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['resource_name']); ?></td>
                                            <td>
                                                <span class="badge bg-secondary">Qty: <?php echo $row['quantity']; ?></span>
                                                <small class="d-block text-muted"><?php echo htmlspecialchars($row['purpose']); ?></small>
                                            </td>
                                            <td>
                                                <?php 
                                                    $badgeBg = 'bg-warning text-dark';
                                                    if ($row['status'] == 'Approved') $badgeBg = 'bg-success';
                                                    else if ($row['status'] == 'Rejected') $badgeBg = 'bg-danger';
                                                ?>
                                                <span class="badge <?php echo $badgeBg; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                            </td>
                                            <td class="text-end">
                                                <a href="resource_requests.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success mb-1" title="Approve"><i class="bi bi-check-lg"></i></a>
                                                <a href="resource_requests.php?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning mb-1" title="Reject"><i class="bi bi-x-lg"></i></a>
                                                <a href="resource_requests.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger mb-1" onclick="return confirm('Delete this request?')" title="Delete"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-3">No data found</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
        $('#requestsTable').DataTable({
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





