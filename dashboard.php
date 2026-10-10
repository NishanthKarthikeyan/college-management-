<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

function getResourceCount($conn, $category) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM resources WHERE category = ?");
    if (!$stmt) return 0;
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res['total'] ?? 0;
}

$classrooms_count = getResourceCount($conn, 'Classroom');
$labs_count       = getResourceCount($conn, 'Computer Lab');
$meeting_count    = getResourceCount($conn, 'Meeting Room');
$seminar_count    = getResourceCount($conn, 'Seminar Hall');
$equipment_count  = getResourceCount($conn, 'Equipment');

$dept_res    = $conn->query("SELECT COUNT(*) as total FROM departments");
$total_depts = ($dept_res && $row = $dept_res->fetch_assoc()) ? $row['total'] : 0;

$notif_query  = "SELECT * FROM notifications WHERE status = 'unread' ORDER BY created_at DESC";
$notif_result = $conn->query($notif_query);
$unread_count = ($notif_result) ? $notif_result->num_rows : 0;

// Ã¢â€â‚¬Ã¢â€â‚¬ Recent Bookings (sidebar table)
$bookings_result = $conn->query("SELECT * FROM bookings ORDER BY id DESC LIMIT 5");

// Ã¢â€â‚¬Ã¢â€â‚¬ [NEW] Summary Metrics
$r = $conn->query("SELECT COUNT(*) as c FROM bookings");             $total_bookings  = $r ? $r->fetch_assoc()['c'] : 0;
$r = $conn->query("SELECT COUNT(*) as c FROM resource_requests WHERE status='Pending'"); $pending_req = $r ? $r->fetch_assoc()['c'] : 0;
$r = $conn->query("SELECT COUNT(*) as c FROM maintenance WHERE status='Pending' OR status='In Progress'"); $active_maint = $r ? $r->fetch_assoc()['c'] : 0;
$r = $conn->query("SELECT COUNT(*) as c FROM resources WHERE status='Available'"); $avail_res = $r ? $r->fetch_assoc()['c'] : 0;

// Ã¢â€â‚¬Ã¢â€â‚¬ [NEW] Monthly Bookings (last 6 months)
$monthly_labels = [];
$monthly_data   = [];
for ($i = 5; $i >= 0; $i--) {
    $label = date('M Y', strtotime("-$i months"));
    $m     = date('m', strtotime("-$i months"));
    $y     = date('Y', strtotime("-$i months"));
    $r     = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE MONTH(booking_date)=$m AND YEAR(booking_date)=$y");
    $monthly_labels[] = $label;
    $monthly_data[]   = $r ? (int)$r->fetch_assoc()['c'] : 0;
}
$monthly_labels_json = json_encode($monthly_labels);
$monthly_data_json   = json_encode($monthly_data);

// Ã¢â€â‚¬Ã¢â€â‚¬ [NEW] Top 5 Most Booked Resources
$top_resources = [];
$r = $conn->query("SELECT resource_name, COUNT(*) as total FROM bookings GROUP BY resource_name ORDER BY total DESC LIMIT 5");
$max_bookings  = 1;
if ($r && $r->num_rows > 0) {
    while ($row = $r->fetch_assoc()) $top_resources[] = $row;
    if (!empty($top_resources)) $max_bookings = max(array_column($top_resources, 'total'));
}

// Ã¢â€â‚¬Ã¢â€â‚¬ [NEW] Today's Schedule
$today = date('Y-m-d');
$today_bookings = $conn->query("SELECT * FROM bookings WHERE booking_date = '$today' ORDER BY start_time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | College RMS</title>
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
        .stat-card { border: none; border-radius: 16px; background: #fff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>
<body>
<div class="d-flex">
    <div class="sidebar">
        <div class="brand"><i class="bi bi-building-gear me-2"></i> College RMS</div>
        <div class="mt-3">
            <a href="dashboard.php" class="active"><i class="bi bi-grid-1x2-fill me-3"></i> Dashboard</a>
            <a href="departments.php"><i class="bi bi-diagram-3-fill me-3"></i> Departments</a>
            <a href="resources.php?type=Classroom"><i class="bi bi-door-open-fill me-3"></i> Classrooms</a>
            <a href="resources.php?type=Computer Lab"><i class="bi bi-laptop-fill me-3"></i> Computer Labs</a>
            <a href="resources.php?type=Meeting Room"><i class="bi bi-people-fill me-3"></i> Meeting Rooms</a>
            <a href="resources.php?type=Seminar Hall"><i class="bi bi-mic-fill me-3"></i> Seminar Hall</a>
            <a href="resources.php?type=Equipment"><i class="bi bi-tools me-3"></i> Equipment</a>
            <a href="bookings.php"><i class="bi bi-calendar2-check-fill me-3"></i> Bookings</a>
            <a href="maintenance.php"><i class="bi bi-wrench-adjustable-circle-fill me-3"></i> Maintenance</a>
            <a href="resource_requests.php"><i class="bi bi-inbox-fill me-3"></i> Resource Requests</a>
            <a href="logout.php" class="text-danger mt-2"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
        </div>
    </div>
    <div class="main-content flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold mb-1">Welcome back, Admin! 👋</h3>
                <p class="text-muted small mb-0">Here is the overview of all college resources</p>
            </div>
            
            <!-- Global Search Bar -->
            <div class="position-relative flex-grow-1" style="max-width: 400px; z-index: 1050;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted shadow-sm" style="border-radius: 20px 0 0 20px;"><i class="bi bi-search"></i></span>
                    <input type="text" id="globalSearch" class="form-control border-start-0 shadow-sm" placeholder="Search resources, bookings, depts..." style="border-radius: 0 20px 20px 0; background: #fff;" autocomplete="off">
                </div>
                <!-- Search Results Dropdown -->
                <div id="searchResults" class="dropdown-menu w-100 shadow-lg border-0 mt-2 p-2" style="display:none; border-radius:12px; max-height: 400px; overflow-y: auto;">
                    <!-- Results will be injected here via AJAX -->
                </div>
            </div>

            <div class="dropdown">
                <button class="btn btn-light position-relative dropdown-toggle border shadow-sm px-3 py-2 bg-white rounded-pill" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill text-primary me-1"></i> Notifications
                    <?php if ($unread_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-3 mt-2" style="width: 350px; max-height: 400px; overflow-y: auto; border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-bell me-2 text-primary"></i>Notifications</h6>
                        <a href="clear_notif.php" class="text-decoration-none text-danger small fw-semibold">Clear All</a>
                    </div>
                    <li><hr class="dropdown-divider"></li>
                    <?php if ($unread_count > 0): ?>
                        <?php while ($notif = $notif_result->fetch_assoc()): ?>
                            <li class="mb-2">
                                <a class="dropdown-item text-wrap p-2 rounded-3 bg-light border-start border-primary border-4 shadow-sm text-decoration-none d-block" href="bookings.php">
                                    <div class="fw-semibold text-dark" style="font-size: 13px;">
                                        <i class="bi bi-info-circle-fill text-primary me-1"></i> <?php echo htmlspecialchars($notif['message']); ?>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 11px;">
                                        <i class="bi bi-clock me-1"></i> <?php echo date('M d, Y - h:i A', strtotime($notif['created_at'])); ?>
                                    </div>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="text-center py-4">
                            <i class="bi bi-check-circle text-muted fs-3 d-block mb-1"></i>
                            <span class="text-muted small">No new notifications</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- ══ QUICK ACTIONS ROW ══ -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <a href="bookings.php" class="btn fw-semibold shadow-sm px-4 py-2" style="background:#4f46e5; color:#fff; border-radius:10px;">
                        <i class="bi bi-calendar-plus me-2"></i>New Booking
                    </a>
                    <a href="resources.php" class="btn fw-semibold shadow-sm px-4 py-2" style="background:#0ea5e9; color:#fff; border-radius:10px;">
                        <i class="bi bi-box-seam me-2"></i>Add Resource
                    </a>
                    <a href="departments.php" class="btn fw-semibold shadow-sm px-4 py-2" style="background:#10b981; color:#fff; border-radius:10px;">
                        <i class="bi bi-building me-2"></i>Add Department
                    </a>
                    <a href="maintenance.php" class="btn bg-white fw-semibold shadow-sm border px-4 py-2 text-danger" style="border-radius:10px;">
                        <i class="bi bi-tools me-2"></i>Report Maintenance
                    </a>
                </div>
            </div>
        </div>
        <!-- ══ SUMMARY METRICS ROW ══ -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;">
                    <div style="font-size:28px;"><i class="bi bi-calendar2-check-fill"></i></div>
                    <div><div style="font-size:24px;font-weight:700;line-height:1;"><?php echo $total_bookings; ?></div><div style="font-size:12px;opacity:0.85;">Total Bookings</div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);color:#fff;">
                    <div style="font-size:28px;"><i class="bi bi-hourglass-split"></i></div>
                    <div><div style="font-size:24px;font-weight:700;line-height:1;"><?php echo $pending_req; ?></div><div style="font-size:12px;opacity:0.85;">Pending Requests</div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#ef4444,#f87171);color:#fff;">
                    <div style="font-size:28px;"><i class="bi bi-wrench-adjustable"></i></div>
                    <div><div style="font-size:24px;font-weight:700;line-height:1;"><?php echo $active_maint; ?></div><div style="font-size:12px;opacity:0.85;">Active Maintenance</div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="p-3 rounded-3 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#10b981,#34d399);color:#fff;">
                    <div style="font-size:28px;"><i class="bi bi-check-circle-fill"></i></div>
                    <div><div style="font-size:24px;font-weight:700;line-height:1;"><?php echo $avail_res; ?></div><div style="font-size:12px;opacity:0.85;">Available Resources</div></div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Left Column: Stats -->
            <div class="col-xl-6">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <span class="text-muted small fw-medium">Total<br>Classrooms</span>
                                    <div class="icon-box bg-primary-subtle text-primary" style="width: 35px; height: 35px; font-size: 16px;"><i class="bi bi-door-open-fill"></i></div>
                                </div>
                                <h2 class="fw-bold mb-3"><?php echo $classrooms_count; ?></h2>
                                <a href="resources.php?type=Classroom" class="btn btn-sm btn-light text-primary fw-medium w-100" style="font-size: 11px;">Manage Classrooms &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <span class="text-muted small fw-medium">Computer<br>Labs</span>
                                    <div class="icon-box bg-info-subtle text-info" style="width: 35px; height: 35px; font-size: 16px;"><i class="bi bi-laptop-fill"></i></div>
                                </div>
                                <h2 class="fw-bold mb-3"><?php echo $labs_count; ?></h2>
                                <a href="resources.php?type=Computer Lab" class="btn btn-sm btn-light text-info fw-medium w-100" style="font-size: 11px;">Manage Labs &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <span class="text-muted small fw-medium">Meeting<br>Rooms</span>
                                    <div class="icon-box bg-warning-subtle text-warning" style="width: 35px; height: 35px; font-size: 16px;"><i class="bi bi-people-fill"></i></div>
                                </div>
                                <h2 class="fw-bold mb-3"><?php echo $meeting_count; ?></h2>
                                <a href="resources.php?type=Meeting Room" class="btn btn-sm btn-light text-warning fw-medium w-100" style="font-size: 11px;">Manage Rooms &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <span class="text-muted small fw-medium">Seminar<br>Halls</span>
                                    <div class="icon-box bg-success-subtle text-success" style="width: 35px; height: 35px; font-size: 16px;"><i class="bi bi-mic-fill"></i></div>
                                </div>
                                <h2 class="fw-bold mb-3"><?php echo $seminar_count; ?></h2>
                                <a href="resources.php?type=Seminar Hall" class="btn btn-sm btn-light text-success fw-medium w-100" style="font-size: 11px;">Manage Halls &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <span class="text-muted small fw-medium">Equipments<br>&nbsp;</span>
                                    <div class="icon-box bg-danger-subtle text-danger" style="width: 35px; height: 35px; font-size: 16px;"><i class="bi bi-tools"></i></div>
                                </div>
                                <h2 class="fw-bold mb-3"><?php echo $equipment_count; ?></h2>
                                <a href="resources.php?type=Equipment" class="btn btn-sm btn-light text-danger fw-medium w-100" style="font-size: 11px;">Manage Equipments &rarr;</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card p-3 h-100">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <span class="text-muted small fw-medium">Total<br>Departments</span>
                                    <div class="icon-box bg-secondary-subtle text-secondary" style="width: 35px; height: 35px; font-size: 16px;"><i class="bi bi-diagram-3-fill"></i></div>
                                </div>
                                <h2 class="fw-bold mb-3"><?php echo $total_depts; ?></h2>
                                <a href="departments.php" class="btn btn-sm btn-light text-secondary fw-medium w-100" style="font-size: 11px;">View Departments &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Column: Chart -->
            <div class="col-xl-3">
                <div class="card p-4 h-100 border-0 rounded-4 shadow-sm">
                    <h6 class="fw-bold mb-4"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Resource Distribution</h6>
                    <div style="height: 250px; display: flex; justify-content: center; align-items: center;">
                        <canvas id="resourceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right Column: Recent Bookings -->
            <div class="col-xl-3">
                <div class="card p-3 h-100 border-0 rounded-4 shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-primary me-2"></i>Recent Bookings Overview</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle mb-0">
                            <thead class="border-bottom">
                                <tr style="font-size: 11px;" class="text-muted">
                                    <th class="ps-0">Resource</th>
                                    <th>Student</th>
                                    <th class="pe-0">Dept</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12px;">
                                <?php if ($bookings_result && $bookings_result->num_rows > 0): ?>
                                    <?php while($row = $bookings_result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-bold ps-0 text-dark"><?php echo htmlspecialchars($row['resource_name'] ?? ''); ?></td>
                                            <td class="text-muted"><?php echo htmlspecialchars($row['booked_by'] ?? 'N/A'); ?></td>
                                            <td class="pe-0 text-muted"><?php echo htmlspecialchars($row['department'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No bookings.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ã¢â€¢ÂÃ¢â€¢Â ROW 2: Monthly Chart + Top Resources + Today's Schedule Ã¢â€¢ÂÃ¢â€¢Â -->
        <div class="row g-4 mb-4">

            <!-- Monthly Bookings Bar Chart -->
            <div class="col-xl-5">
                <div class="card p-4 h-100 border-0 rounded-4 shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Monthly Bookings (Last 6 Months)</h6>
                    <div style="height: 220px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top 5 Most Booked Resources -->
            <div class="col-xl-4">
                <div class="card p-4 h-100 border-0 rounded-4 shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Most Booked</h6>
                    <?php if (!empty($top_resources)): ?>
                        <?php foreach ($top_resources as $i => $res): ?>
                        <?php
                            $pct = $max_bookings > 0 ? round(($res['total'] / $max_bookings) * 100) : 0;
                            $colors = ['#4f46e5','#0891b2','#059669','#d97706','#dc2626'];
                            $c = $colors[$i];
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size:13px;font-weight:600;color:#1e293b;">
                                    <?php if ($i === 0): ?><i class="bi bi-trophy-fill text-warning me-1"></i><?php endif; ?>
                                    <?php echo htmlspecialchars($res['resource_name']); ?>
                                </span>
                                <span class="badge" style="background:<?php echo $c; ?>20;color:<?php echo $c; ?>;font-size:11px;"><?php echo $res['total']; ?> bookings</span>
                            </div>
                            <div class="progress" style="height:6px;border-radius:10px;">
                                <div class="progress-bar" style="width:<?php echo $pct; ?>%;background:<?php echo $c; ?>;border-radius:10px;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">No booking data yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="col-xl-3">
                <div class="card p-4 h-100 border-0 rounded-4 shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="bi bi-calendar-day-fill text-success me-2"></i>Today's Schedule
                        <small class="text-muted fw-normal" style="font-size:11px;"> Ã¢â‚¬â€ <?php echo date('d M Y'); ?></small>
                    </h6>
                    <?php if ($today_bookings && $today_bookings->num_rows > 0): ?>
                        <div style="max-height:220px;overflow-y:auto;">
                        <?php while ($tb = $today_bookings->fetch_assoc()): ?>
                            <div class="mb-3 p-2 rounded-3" style="background:#f0f3f8;border-left:3px solid #4f46e5;">
                                <div style="font-size:12px;font-weight:700;color:#1e293b;"><?php echo htmlspecialchars($tb['resource_name'] ?? ''); ?></div>
                                <div style="font-size:11px;color:#6b7280;">
                                    <i class="bi bi-clock me-1"></i><?php echo htmlspecialchars(substr($tb['start_time'] ?? '',0,5)); ?> Ã¢â‚¬â€œ <?php echo htmlspecialchars(substr($tb['end_time'] ?? '',0,5)); ?>
                                </div>
                                <div style="font-size:11px;color:#6b7280;">
                                    <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($tb['booked_by'] ?? 'N/A'); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x text-muted" style="font-size:32px;"></i>
                            <p class="text-muted small mt-2 mb-0">No bookings today</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php if ($unread_count > 0): ?>
<audio id="notifAudio" autoplay>
    <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
</audio>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Ã¢â€â‚¬Ã¢â€â‚¬ Donut: Resource Distribution
        new Chart(document.getElementById('resourceChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Classrooms', 'Computer Labs', 'Meeting Rooms', 'Seminar Halls', 'Equipments'],
                datasets: [{ data: [<?php echo $classrooms_count; ?>, <?php echo $labs_count; ?>, <?php echo $meeting_count; ?>, <?php echo $seminar_count; ?>, <?php echo $equipment_count; ?>],
                    backgroundColor: ['#4f46e5','#0dcaf0','#ffc107','#198754','#dc3545'], borderWidth: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { font: { family: "'Poppins', sans-serif" }, boxWidth: 12, padding: 12, font: { size: 11 } } } }
            }
        });

        // Ã¢â€â‚¬Ã¢â€â‚¬ Bar: Monthly Bookings
        new Chart(document.getElementById('monthlyChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo $monthly_labels_json; ?>,
                datasets: [{
                    label: 'Bookings',
                    data: <?php echo $monthly_data_json; ?>,
                    backgroundColor: 'rgba(79,70,229,0.15)',
                    borderColor: '#4f46e5',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: "'Poppins', sans-serif", size: 11 } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { ticks: { font: { family: "'Poppins', sans-serif", size: 11 } }, grid: { display: false } }
                }
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('globalSearch');
        const resultsBox = document.getElementById('searchResults');
        
        let timeout = null;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const q = this.value.trim();
            
            if (q.length < 2) {
                resultsBox.style.display = 'none';
                return;
            }
            
            timeout = setTimeout(() => {
                fetch('ajax_search.php?q=' + encodeURIComponent(q))
                .then(res => res.json())
                .then(data => {
                    resultsBox.innerHTML = '';
                    if (data.length === 0) {
                        resultsBox.innerHTML = '<div class="text-muted text-center p-3 small">No results found for "'+q+'"</div>';
                    } else {
                        data.forEach(item => {
                            resultsBox.innerHTML += 
                                <a href=" + item.url + " class="dropdown-item text-wrap p-2 rounded-3 mb-1 bg-light border-start border-4 shadow-sm text-decoration-none d-block">
                                    <div class="fw-semibold text-dark" style="font-size: 13px;">
                                        <i class="bi  + item.icon +  me-2"></i> + item.title + 
                                        <span class="badge bg-secondary ms-2 opacity-50" style="font-size:10px;">+item.type+</span>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 11px;">
                                         + item.desc + 
                                    </div>
                                </a>;
                        });
                    }
                    resultsBox.style.display = 'block';
                })
                .catch(err => console.error(err));
            }, 300); // 300ms debounce
        });
        
        // Hide when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.style.display = 'none';
            }
        });
        
        // Show again when clicking on input if there's text
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) resultsBox.style.display = 'block';
        });
    });
</script>
<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>







