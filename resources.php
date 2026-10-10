<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) ||
    $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$type = $_GET['type'] ?? 'Classroom';

$allowedTypes = [
    'Classroom', 'Computer Lab', 'Meeting Room',
    'Seminar Hall', 'Equipment'
];

if (!in_array($type, $allowedTypes, true)) {
    $type = 'Classroom';
}

$message = '';

/* Create required columns without deleting old records */
$conn->query("CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dept_code VARCHAR(20),
    dept_name VARCHAR(100) NOT NULL
)");

$columns = [];
$check = $conn->query("SHOW COLUMNS FROM resources");

while ($check && $c = $check->fetch_assoc()) {
    $columns[] = $c['Field'];
}

$newColumns = [
    'department'   => "VARCHAR(100) DEFAULT NULL",
    'capacity'     => "INT DEFAULT 0",
    'quantity'     => "INT DEFAULT 0",
    'resource_code'=> "VARCHAR(50) DEFAULT NULL",
    'floor_number' => "VARCHAR(30) DEFAULT NULL"
];

foreach ($newColumns as $column => $definition) {
    if (!in_array($column, $columns, true)) {
        $conn->query(
            "ALTER TABLE resources ADD COLUMN `$column` $definition"
        );
    }
}

$columns = [];
$check = $conn->query("SHOW COLUMNS FROM resources");

while ($check && $c = $check->fetch_assoc()) {
    $columns[] = $c['Field'];
}

$nameCol = in_array('name', $columns) ? 'name' :
    (in_array('resource_name', $columns) ? 'resource_name' :
    (in_array('title', $columns) ? 'title' : ''));

$categoryCol = in_array('category', $columns) ? 'category' :
    (in_array('type', $columns) ? 'type' : '');

$departmentCol = in_array('department', $columns) ? 'department' :
    (in_array('dept', $columns) ? 'dept' : '');

$valueCol = ($type === 'Equipment')
    ? (in_array('quantity', $columns) ? 'quantity' : 'capacity')
    : (in_array('capacity', $columns) ? 'capacity' : 'quantity');

if ($nameCol === '' || $categoryCol === '') {
    die("Please check the resources table columns in your database.");
}

/* Section-specific number labels */
$numberLabels = [
    'Classroom'   => 'Room Number',
    'Computer Lab'=> 'Lab Number',
    'Meeting Room'=> 'Meeting Room Number',
    'Seminar Hall'=> 'Hall Number',
    'Equipment'   => 'Equipment Number'
];

$numberExamples = [
    'Classroom'   => 'Room 101',
    'Computer Lab'=> 'Lab 01',
    'Meeting Room'=> 'MR-01',
    'Seminar Hall'=> 'Hall 01',
    'Equipment'   => 'EQ-001'
];

$hasFloor = ($type !== 'Equipment');

/* Departments list */
$deptResult = $conn->query("SELECT * FROM departments");
$departments = [];

while ($deptResult && $d = $deptResult->fetch_assoc()) {
    $deptName = $d['dept_name'] ??
                $d['name'] ??
                $d['department_name'] ?? '';

    if ($deptName !== '') {
        $departments[] = $deptName;
    }
}

/* Helper: prepared statement with dynamic parameters */
function runStatement($conn, $sql, $types, $values) {
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    if ($types !== '') {
        $refs = [];
        foreach ($values as $key => $value) {
            $refs[$key] = &$values[$key];
        }
        $stmt->bind_param($types, ...$refs);
    }

    $ok = $stmt->execute();
    $error = $stmt->error;
    $stmt->close();

    return $ok ? true : $error;
}

/* ADD */
if (isset($_POST['add_resource'])) {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['resource_code'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $floor = $hasFloor ? ($_POST['floor_number'] ?? '') : '';
    $value = max(0, (int)($_POST['capacity'] ?? 0));
    $status = $_POST['status'] ?? 'Available';

    if ($name === '' || $code === '' || $department === '' ||
        ($hasFloor && $floor === '')) {
        $message = "Please fill in all required fields.";
    } elseif (!in_array($status, ['Available', 'Maintenance', 'Occupied'], true)) {
        $message = "Invalid status.";
    } else {
        $fields = [$nameCol, $categoryCol, 'resource_code'];
        $values = [$name, $type, $code];
        $types = 'sss';

        if ($departmentCol !== '') {
            $fields[] = $departmentCol;
            $values[] = $department;
            $types .= 's';
        }

        if ($hasFloor) {
            $fields[] = 'floor_number';
            $values[] = $floor;
            $types .= 's';
        }

        $fields[] = $valueCol;
        $values[] = $value;
        $types .= 'i';

        if (in_array('status', $columns, true)) {
            $fields[] = 'status';
            $values[] = $status;
            $types .= 's';
        }

        $fieldSQL = '`' . implode('`,`', $fields) . '`';
        $marks = implode(',', array_fill(0, count($fields), '?'));

        $ok = runStatement(
            $conn,
            "INSERT INTO resources ($fieldSQL) VALUES ($marks)",
            $types,
            $values
        );

        if ($ok === true) {
            header("Location: resources.php?type=" . urlencode($type) . "&msg=" . urlencode($type . " added successfully"));
            exit();
        }

        $message = "Unable to add resource: " . $ok;
    }
}

/* UPDATE */
if (isset($_POST['update_resource'])) {
    $id = (int)($_POST['resource_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['resource_code'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $floor = $hasFloor ? ($_POST['floor_number'] ?? '') : '';
    $value = max(0, (int)($_POST['capacity'] ?? 0));
    $status = $_POST['status'] ?? 'Available';

    if ($id <= 0 || $name === '' || $code === '' ||
        $department === '' || ($hasFloor && $floor === '')) {
        $message = "Please fill in all required fields.";
    } elseif (!in_array($status, ['Available', 'Maintenance', 'Occupied'], true)) {
        $message = "Invalid status.";
    } else {
        $updates = ["`$nameCol` = ?", "`resource_code` = ?"];
        $values = [$name, $code];
        $types = 'ss';

        if ($departmentCol !== '') {
            $updates[] = "`$departmentCol` = ?";
            $values[] = $department;
            $types .= 's';
        }

        if ($hasFloor) {
            $updates[] = "`floor_number` = ?";
            $values[] = $floor;
            $types .= 's';
        }

        $updates[] = "`$valueCol` = ?";
        $values[] = $value;
        $types .= 'i';

        if (in_array('status', $columns, true)) {
            $updates[] = "`status` = ?";
            $values[] = $status;
            $types .= 's';
        }

        $values[] = $id;
        $values[] = $type;
        $types .= 'is';

        $ok = runStatement(
            $conn,
            "UPDATE resources SET " . implode(', ', $updates) .
            " WHERE id = ? AND `$categoryCol` = ?",
            $types,
            $values
        );

        if ($ok === true) {
            header("Location: resources.php?type=" . urlencode($type) . "&msg=" . urlencode($type . " updated successfully"));
            exit();
        }

        $message = "Unable to update resource: " . $ok;
    }
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    if ($id > 0) {
        $ok = runStatement(
            $conn,
            "DELETE FROM resources WHERE id = ? AND `$categoryCol` = ?",
            'is',
            [$id, $type]
        );

        if ($ok === true) {
            header("Location: resources.php?type=" . urlencode($type) . "&msg=" . urlencode($type . " deleted successfully"));
            exit();
        }

        $message = "Unable to delete resource.";
    }
}

/* EDIT */
$editRow = null;

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];

    $editStmt = $conn->prepare(
        "SELECT * FROM resources WHERE id = ? AND `$categoryCol` = ?"
    );

    if ($editStmt) {
        $editStmt->bind_param('is', $editId, $type);
        $editStmt->execute();
        $editRow = $editStmt->get_result()->fetch_assoc();
        $editStmt->close();
    }
}

/* List */
$listStmt = $conn->prepare(
    "SELECT * FROM resources WHERE `$categoryCol` = ? ORDER BY id DESC"
);
$listStmt->bind_param('s', $type);
$listStmt->execute();
$result = $listStmt->get_result();

function showName($row, $nameCol) {
    return $row[$nameCol] ?? '';
}

function showValue($row, $type) {
    return (int)(($type === 'Equipment')
        ? ($row['quantity'] ?? $row['capacity'] ?? 0)
        : ($row['capacity'] ?? $row['quantity'] ?? 0));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage <?php echo htmlspecialchars($type); ?> | PKR College RMS</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<!-- DataTables & Export CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<style>
* { font-family: 'Poppins', sans-serif; }
body { background: #f0f3f8; color: #1e293b; margin: 0; }

.sidebar {
    min-height: 100vh;
    background: linear-gradient(180deg, #0f172a, #1e293b);
    color: white;
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
}
.brand {
    font-size: 19px;
    font-weight: 700;
    padding: 22px 18px;
    color: #818cf8;
    border-bottom: 1px solid #334155;
}
.sidebar a {
    color: #cbd5e1;
    text-decoration: none;
    padding: 12px 18px;
    display: block;
    font-size: 13px;
    border-radius: 8px;
    margin: 5px 10px;
}
.sidebar a:hover, .sidebar a.active {
    background: #4f46e5;
    color: white;
}
.main-content {
    margin-left: 250px;
    padding: 30px;
    width: calc(100% - 250px);
}
.card-box {
    background: white;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,.05);
}
.table th, .table td { white-space: nowrap; font-size: 13px; }

@media(max-width: 800px) {
    .sidebar { width: 175px; }
    .main-content {
        margin-left: 175px;
        width: calc(100% - 175px);
        padding: 15px;
    }
}
</style>
    <link rel="stylesheet" href="mobile_view.css">
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <i class="bi bi-building-gear me-2"></i>PKR College RMS
    </div>

    <a href="dashboard.php"><i class="bi bi-grid me-2"></i>Dashboard</a>
    <a href="departments.php"><i class="bi bi-diagram-3 me-2"></i>Departments</a>

    <a href="resources.php?type=Classroom" class="<?php echo $type === 'Classroom' ? 'active' : ''; ?>">
        <i class="bi bi-door-open me-2"></i>Classrooms
    </a>
    <a href="resources.php?type=Computer Lab" class="<?php echo $type === 'Computer Lab' ? 'active' : ''; ?>">
        <i class="bi bi-laptop me-2"></i>Computer Labs
    </a>
    <a href="resources.php?type=Meeting Room" class="<?php echo $type === 'Meeting Room' ? 'active' : ''; ?>">
        <i class="bi bi-people me-2"></i>Meeting Rooms
    </a>
    <a href="resources.php?type=Seminar Hall" class="<?php echo $type === 'Seminar Hall' ? 'active' : ''; ?>">
        <i class="bi bi-mic me-2"></i>Seminar Hall
    </a>
    <a href="resources.php?type=Equipment" class="<?php echo $type === 'Equipment' ? 'active' : ''; ?>">
        <i class="bi bi-tools me-2"></i>Equipment
    </a>

    <a href="bookings.php"><i class="bi bi-calendar-check me-2"></i>Bookings</a>
    <a href="maintenance.php"><i class="bi bi-wrench me-2"></i>Maintenance</a>
    <a href="resource_requests.php"><i class="bi bi-inbox me-2"></i>Resource Requests</a>
            <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
</div>

<div class="main-content">

<h3 class="fw-bold mb-4">Manage <?php echo htmlspecialchars($type); ?></h3>

<?php if ($message !== ''): ?>
<div class="alert alert-warning"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="row g-4">

<div class="col-lg-4">
<div class="card-box p-4">

<h5 class="fw-bold mb-3">
    <?php echo $editRow ? 'Edit ' : 'Add '; ?>
    <?php echo htmlspecialchars($type); ?>
</h5>

<form method="POST">

<?php if ($editRow): ?>
<input type="hidden" name="resource_id" value="<?php echo (int)$editRow['id']; ?>">
<?php endif; ?>

<div class="mb-3">
<label class="form-label"><?php echo htmlspecialchars($numberLabels[$type]); ?></label>
<input type="text" name="resource_code" class="form-control"
       placeholder="<?php echo htmlspecialchars($numberExamples[$type]); ?>"
       value="<?php echo htmlspecialchars($editRow['resource_code'] ?? ''); ?>"
       required>
</div>

<?php if ($hasFloor): ?>
<div class="mb-3">
<label class="form-label">Floor Number</label>
<select name="floor_number" class="form-select" required>
<option value="">Select Floor</option>
<?php foreach (['Ground Floor', '1st Floor', '2nd Floor', '3rd Floor'] as $floor): ?>
<option value="<?php echo htmlspecialchars($floor); ?>"
<?php echo (($editRow['floor_number'] ?? '') === $floor) ? 'selected' : ''; ?>>
<?php echo htmlspecialchars($floor); ?>
</option>
<?php endforeach; ?>
</select>
</div>
<?php endif; ?>

<div class="mb-3">
<label class="form-label">Name</label>
<input type="text" name="name" class="form-control"
       placeholder="Enter resource name"
       value="<?php echo htmlspecialchars($editRow ? showName($editRow, $nameCol) : ''); ?>"
       required>
</div>

<div class="mb-3">
<label class="form-label">Department</label>
<select name="department" class="form-select" required>
<option value="">Select Department</option>
<?php foreach ($departments as $dept): ?>
<option value="<?php echo htmlspecialchars($dept); ?>"
<?php echo (($editRow['department'] ?? $editRow['dept'] ?? '') === $dept) ? 'selected' : ''; ?>>
<?php echo htmlspecialchars($dept); ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="mb-3">
<label class="form-label"><?php echo $type === 'Equipment' ? 'Quantity' : 'Capacity'; ?></label>
<input type="number" name="capacity" class="form-control" min="0" required
       value="<?php echo $editRow ? showValue($editRow, $type) : ($type === 'Equipment' ? 1 : 40); ?>">
</div>

<div class="mb-3">
<label class="form-label">Status</label>
<select name="status" class="form-select">
<?php foreach (['Available', 'Maintenance', 'Occupied'] as $status): ?>
<option value="<?php echo $status; ?>"
<?php echo (($editRow['status'] ?? 'Available') === $status) ? 'selected' : ''; ?>>
<?php echo $status; ?>
</option>
<?php endforeach; ?>
</select>
</div>

<button type="submit"
        name="<?php echo $editRow ? 'update_resource' : 'add_resource'; ?>"
        class="btn btn-primary w-100">
<i class="bi bi-<?php echo $editRow ? 'check-circle' : 'plus-circle'; ?> me-1"></i>
<?php echo $editRow ? 'Update ' : 'Add '; ?><?php echo htmlspecialchars($type); ?>
</button>

<?php if ($editRow): ?>
<a href="resources.php?type=<?php echo urlencode($type); ?>"
   class="btn btn-secondary w-100 mt-2">Cancel</a>
<?php endif; ?>

</form>
</div>
</div>

<div class="col-lg-8">
<div class="card-box p-4">

<h5 class="fw-bold mb-3"><?php echo htmlspecialchars($type); ?> List</h5>

<div class="table-responsive mt-3">
<table id="resourcesTable" class="table table-hover align-middle table-striped">

<thead class="table-light">
<tr>
<?php if ($hasFloor): ?>
<th>Floor Number</th>
<?php endif; ?>

<th><?php echo htmlspecialchars($numberLabels[$type]); ?></th>
<th>Name</th>
<th>Department</th>
<th><?php echo $type === 'Equipment' ? 'Quantity' : 'Capacity'; ?></th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php if ($result && $result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>

<?php if ($hasFloor): ?>
<td><?php echo htmlspecialchars($row['floor_number'] ?? '-'); ?></td>
<?php endif; ?>

<td><?php echo htmlspecialchars($row['resource_code'] ?? '-'); ?></td>

<td><?php echo htmlspecialchars(showName($row, $nameCol)); ?></td>

<td><?php echo htmlspecialchars($row['department'] ?? $row['dept'] ?? '-'); ?></td>

<td><?php echo showValue($row, $type); ?></td>

<td>
<span class="badge <?php
echo ($row['status'] ?? 'Available') === 'Available'
    ? 'bg-success-subtle text-success'
    : 'bg-warning-subtle text-warning';
?>">
<?php echo htmlspecialchars($row['status'] ?? 'Available'); ?>
</span>
</td>

<td>
<div class="d-flex gap-1">
<a href="resources.php?type=<?php echo urlencode($type); ?>&edit=<?php echo (int)$row['id']; ?>"
   class="btn btn-sm btn-outline-primary" title="Edit">
<i class="bi bi-pencil-square"></i>
</a>

<a href="resources.php?type=<?php echo urlencode($type); ?>&delete=<?php echo (int)$row['id']; ?>"
   class="btn btn-sm btn-outline-danger"
   onclick="return confirm('Are you sure you want to delete this resource?');"
   title="Delete">
<i class="bi bi-trash"></i>
</a>
</div>
</td>

</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="7" class="text-center text-muted py-4">No records found.</td>
</tr>
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
        $('#resourcesTable').DataTable({
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
                    orientation: 'landscape'
                }
            ],
            pageLength: 10,
            order: [[1, 'asc']]
        });
    });
</script>
<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>




