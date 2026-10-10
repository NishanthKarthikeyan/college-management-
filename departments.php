<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Ensure departments table exists with matching database structure
$conn->query("CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dept_code VARCHAR(20),
    dept_name VARCHAR(100) NOT NULL
)");

// Handle Add Department
if (isset($_POST['add_dept'])) {
    $dept_name = trim($_POST['dept_name']);
    if (!empty($dept_name)) {
        $stmt = $conn->prepare("INSERT INTO departments (dept_name) VALUES (?)");
        $stmt->bind_param("s", $dept_name);
        $stmt->execute();
        header("Location: departments.php?msg=Department added successfully");
        exit();
    }
}

// Handle Delete Department
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM departments WHERE id = $id");
    header("Location: departments.php?msg=Department deleted successfully");
    exit();
}

$result = $conn->query("SELECT * FROM departments ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments | College RMS</title>
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
        <h3 class="fw-bold mb-4">Manage Departments</h3>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-box p-4">
                    <h5 class="fw-bold mb-3">Add New Department</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Department Name</label>
                            <input type="text" name="dept_name" class="form-control" placeholder="e.g. Computer Science" required>
                        </div>
                        <button type="submit" name="add_dept" class="btn btn-primary w-100 fw-medium">Add Department</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card-box p-4">
                    <h5 class="fw-bold mb-3">Department List</h5>
                    <div class="table-responsive mt-3">
                        <table id="deptTable" class="table align-middle table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Department Name</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $sno = 1; while ($row = $result->fetch_assoc()): 
                                        $deptName = $row['dept_name'] ?? ($row['name'] ?? 'Unknown');
                                    ?>
                                        <tr>
                                            <td class="text-muted"><?php echo $sno++; ?></td>
                                            <td class="fw-medium"><?php echo htmlspecialchars($deptName); ?></td>
                                            <td class="text-end">
                                                <a href="edit_department.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                                <a href="departments.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this department?')" title="Delete"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No departments found.</td>
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
        $('#deptTable').DataTable({
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: { columns: [0, 1] }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: { columns: [0, 1] }
                }
            ],
            pageLength: 10,
            order: [[0, 'asc']]
        });
    });
</script>
<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>







