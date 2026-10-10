<?php
// Usage: include 'sidebar.php'; (pass $activePage variable before including)
// Example: $activePage = 'dashboard'; include 'sidebar.php';

$activePage = $activePage ?? '';

$navLinks = [
    ['page' => 'dashboard',         'href' => 'dashboard.php',                'icon' => 'bi-grid-1x2-fill',                'label' => 'Dashboard'],
    ['page' => 'departments',       'href' => 'departments.php',               'icon' => 'bi-diagram-3-fill',               'label' => 'Departments'],
    ['page' => 'classroom',         'href' => 'resources.php?type=Classroom',  'icon' => 'bi-door-open-fill',               'label' => 'Classrooms'],
    ['page' => 'computer_lab',      'href' => 'resources.php?type=Computer Lab','icon' => 'bi-laptop-fill',                 'label' => 'Computer Labs'],
    ['page' => 'meeting_room',      'href' => 'resources.php?type=Meeting Room','icon' => 'bi-people-fill',                 'label' => 'Meeting Rooms'],
    ['page' => 'seminar_hall',      'href' => 'resources.php?type=Seminar Hall','icon' => 'bi-mic-fill',                    'label' => 'Seminar Hall'],
    ['page' => 'equipment',         'href' => 'resources.php?type=Equipment',  'icon' => 'bi-tools',                        'label' => 'Equipment'],
    ['page' => 'bookings',          'href' => 'bookings.php',                  'icon' => 'bi-calendar2-check-fill',         'label' => 'Bookings'],
    ['page' => 'maintenance',       'href' => 'maintenance.php',               'icon' => 'bi-wrench-adjustable-circle-fill','label' => 'Maintenance'],
    ['page' => 'resource_requests', 'href' => 'resource_requests.php',         'icon' => 'bi-inbox-fill',                   'label' => 'Resource Requests'],
];
?>
<div class="sidebar">
    <div class="brand"><i class="bi bi-building-gear me-2"></i> College RMS</div>
    <div class="mt-3">
        <?php foreach ($navLinks as $link): ?>
        <a href="<?php echo $link['href']; ?>" <?php echo ($activePage === $link['page']) ? 'class="active"' : ''; ?>>
            <i class="bi <?php echo $link['icon']; ?> me-3"></i> <?php echo $link['label']; ?>
        </a>
        <?php endforeach; ?>

        <!-- Dark Mode Toggle -->
        <button class="dark-toggle" id="darkToggle" onclick="toggleDarkMode()">
            <i class="bi bi-moon-fill" id="darkIcon"></i>
            <span id="darkLabel">Dark Mode</span>
        </button>

        <a href="logout.php" class="text-danger mt-2"><i class="bi bi-box-arrow-right me-3"></i> Logout</a>
    </div>
</div>
