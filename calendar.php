<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Fetch all bookings for the calendar
$bookings_result = $conn->query("SELECT * FROM bookings ORDER BY booking_date DESC");
$events = [];

$colors = ['#4f46e5','#0891b2','#059669','#d97706','#dc2626','#7c3aed','#db2777','#0284c7'];
$deptColorMap = [];
$colorIdx = 0;

if ($bookings_result && $bookings_result->num_rows > 0) {
    while($row = $bookings_result->fetch_assoc()) {
        $dept = $row['department'] ?? 'General';
        if (!isset($deptColorMap[$dept])) {
            $deptColorMap[$dept] = $colors[$colorIdx % count($colors)];
            $colorIdx++;
        }
        $start_datetime = $row['booking_date'] . 'T' . ($row['start_time'] ?? '08:00:00');
        $end_datetime   = $row['booking_date'] . 'T' . ($row['end_time']   ?? '09:00:00');

        $events[] = [
            'id'          => $row['id'],
            'title'       => $row['resource_name'] . ' â€” ' . ($row['booked_by'] ?? ''),
            'start'       => $start_datetime,
            'end'         => $end_datetime,
            'color'       => $deptColorMap[$dept],
            'description' => 'Dept: ' . $dept,
            'editable'    => true,
        ];
    }
}
$events_json = json_encode($events);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Calendar | College RMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        :root {
            --bg: #f0f3f8; --surface: #ffffff; --text: #1e293b;
            --text-muted: #64748b; --border: #e2e8f0;
        }
        [data-theme="dark"] {
            --bg: #0f172a; --surface: #1e293b; --text: #e2e8f0;
            --text-muted: #94a3b8; --border: #334155;
        }
        * { font-family: 'Poppins', sans-serif; transition: background-color 0.3s, color 0.3s; }
        body { background-color: var(--bg); color: var(--text); }

        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #fff; width: 250px; position: fixed; z-index: 1000; }
        .sidebar .brand { font-size: 20px; font-weight: 700; padding: 22px 20px; color: #6366f1; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 20px; display: flex; align-items: center; font-size: 14px; font-weight: 500; border-radius: 8px; margin: 5px 12px; transition: all 0.3s; }
        .sidebar a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }
        .sidebar a.active { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #fff; }
        .main-content { margin-left: 250px; padding: 35px; }

        /* Dark mode toggle */
        .dark-toggle { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); color: #94a3b8; border-radius: 8px; padding: 8px 14px; cursor: pointer; font-size: 13px; transition: all 0.3s; display: flex; align-items: center; gap: 6px; }
        .dark-toggle:hover { background: rgba(255,255,255,0.2); color: #fff; }

        /* Calendar */
        #calendar { background: var(--surface); padding: 24px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .fc-event { border: none !important; border-radius: 6px !important; padding: 2px 6px !important; font-size: 12px !important; cursor: grab !important; }
        .fc-event:active { cursor: grabbing !important; }
        .fc-toolbar-title { font-weight: 700 !important; }
        .fc-button-primary { background: linear-gradient(135deg, #4f46e5, #6366f1) !important; border: none !important; border-radius: 8px !important; }
        .fc-button-primary:hover { opacity: 0.9 !important; }
        [data-theme="dark"] .fc-theme-standard td, [data-theme="dark"] .fc-theme-standard th { border-color: #334155; }
        [data-theme="dark"] .fc-col-header-cell-cushion, [data-theme="dark"] .fc-daygrid-day-number { color: #e2e8f0; }
        [data-theme="dark"] .fc-daygrid-day { background: #1e293b; }
        [data-theme="dark"] .fc-day-today { background: #273549 !important; }
        [data-theme="dark"] .fc-toolbar-title { color: #e2e8f0; }

        /* Legend */
        .legend-box { display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 5px; }

        /* Modal */
        .modal-content { background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 16px; }
        .modal-header { border-color: var(--border); }
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
            <div class="px-3 mt-3">
                <button class="dark-toggle w-100" id="darkToggle" onclick="toggleDarkMode()">
                    <i class="bi bi-moon-fill" id="darkIcon"></i>
                    <span id="darkLabel">Dark Mode</span>
                </button>
            </div>
            <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
        </div>
    </div>

    <div class="main-content flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">ðŸ“… Booking Calendar</h3>
                <small class="text-muted">Drag & drop events to reschedule bookings</small>
            </div>
            <a href="bookings.php" class="btn btn-outline-primary"><i class="bi bi-list-ul me-2"></i>List View</a>
        </div>

        <!-- Dept Color Legend -->
        <?php if (!empty($deptColorMap)): ?>
        <div class="mb-3 d-flex flex-wrap gap-3">
            <?php foreach ($deptColorMap as $dept => $color): ?>
            <span class="small fw-medium">
                <span class="legend-box" style="background:<?php echo $color; ?>"></span>
                <?php echo htmlspecialchars($dept); ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div id='calendar'></div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3 mb-3 p-3 rounded-3" id="modalColorBand" style="border-left: 4px solid #4f46e5; background: rgba(79,70,229,0.05);">
                    <div>
                        <div class="fw-bold mb-1" id="modalResource">â€”</div>
                        <div class="text-muted small" id="modalDept">â€”</div>
                    </div>
                </div>
                <div class="row g-2 small">
                    <div class="col-6">
                        <div class="p-2 rounded-2" style="background: var(--bg);">
                            <div class="text-muted" style="font-size:11px;">DATE</div>
                            <div class="fw-semibold" id="modalDate">â€”</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2" style="background: var(--bg);">
                            <div class="text-muted" style="font-size:11px;">TIME</div>
                            <div class="fw-semibold" id="modalTime">â€”</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <a href="#" class="btn btn-primary btn-sm" id="modalEditBtn"><i class="bi bi-pencil me-1"></i>Edit</a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Drag confirm toast -->
<div id="dragToast" style="display:none; position:fixed; bottom:24px; right:24px; z-index:9999;
    background: linear-gradient(135deg,#059669,#047857); color:#fff; padding:14px 20px;
    border-radius:12px; box-shadow:0 10px 30px rgba(5,150,105,0.35); font-size:14px; font-weight:500;">
    <i class="bi bi-check-circle-fill me-2"></i><span id="dragToastMsg">Booking updated!</span>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // â”€â”€â”€ Dark Mode â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        const icon  = document.getElementById('darkIcon');
        const label = document.getElementById('darkLabel');
        if (theme === 'dark') {
            icon.className  = 'bi bi-sun-fill';
            label.textContent = 'Light Mode';
        } else {
            icon.className  = 'bi bi-moon-fill';
            label.textContent = 'Dark Mode';
        }
    }
    function toggleDarkMode() {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('rms_theme', next);
        applyTheme(next);
    }
    // Apply saved theme on load
    const savedTheme = localStorage.getItem('rms_theme') || 'light';
    applyTheme(savedTheme);

    // â”€â”€â”€ Calendar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: <?php echo $events_json; ?>,
            editable: true,       // enables drag & drop
            droppable: true,
            eventResizableFromStart: true,

            // â”€â”€ Click to view details â”€â”€
            eventClick: function(info) {
                const ev  = info.event;
                const start = ev.start;
                const end   = ev.end;

                document.getElementById('modalTitle').textContent = ev.title;
                document.getElementById('modalResource').textContent = ev.title;
                document.getElementById('modalDept').textContent = ev.extendedProps.description || '';
                document.getElementById('modalDate').textContent = start ? start.toLocaleDateString('en-IN', {weekday:'short', day:'numeric', month:'short', year:'numeric'}) : 'â€”';
                document.getElementById('modalTime').textContent = (start ? start.toLocaleTimeString('en-IN', {hour:'2-digit', minute:'2-digit'}) : '') + ' â€“ ' + (end ? end.toLocaleTimeString('en-IN', {hour:'2-digit', minute:'2-digit'}) : '');
                document.getElementById('modalEditBtn').href = 'edit_booking.php?id=' + ev.id;
                document.getElementById('modalColorBand').style.borderLeftColor = ev.backgroundColor;
                document.getElementById('modalColorBand').style.background = ev.backgroundColor + '15';

                eventModal.show();
            },

            // â”€â”€ Drag & Drop â”€â”€
            eventDrop: function(info) {
                updateBookingDate(info.event, info.revert);
            },

            // â”€â”€ Resize â”€â”€
            eventResize: function(info) {
                updateBookingDate(info.event, info.revert);
            },

            // Tooltip on hover
            eventDidMount: function(info) {
                info.el.setAttribute('title', info.event.extendedProps.description || info.event.title);
            }
        });

        calendar.render();

        function updateBookingDate(event, revert) {
            const payload = {
                id:    event.id,
                start: event.start ? event.start.toISOString() : null,
                end:   event.end   ? event.end.toISOString()   : null,
            };

            fetch('update_booking_date.php', {
                method:  'POST',
                headers: {'Content-Type': 'application/json'},
                body:    JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    showDragToast('âœ… Booking rescheduled successfully!');
                    // Also save a notification
                    fetch('save_notification.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ message: 'Booking rescheduled: ' + event.title })
                    });
                } else {
                    revert();
                    showDragToast('âŒ Failed to update booking.', true);
                }
            })
            .catch(() => { revert(); showDragToast('âŒ Network error.', true); });
        }

        function showDragToast(msg, isError) {
            const toast = document.getElementById('dragToast');
            document.getElementById('dragToastMsg').textContent = msg;
            toast.style.background = isError
                ? 'linear-gradient(135deg,#dc2626,#b91c1c)'
                : 'linear-gradient(135deg,#059669,#047857)';
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 3500);
        }
    });
</script>
<script src="voice_notification.js?v=6"></script>
    <script src="mobile_view.js"></script>
</body>
</html>



