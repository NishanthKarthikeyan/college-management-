<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode([]);
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$search = "%{$q}%";
$results = [];

// 1. Search Resources
$stmt1 = $conn->prepare("SELECT resource_name, category, status FROM resources WHERE resource_name LIKE ? OR category LIKE ? LIMIT 3");
$stmt1->bind_param("ss", $search, $search);
$stmt1->execute();
$res1 = $stmt1->get_result();
while ($row = $res1->fetch_assoc()) {
    $results[] = [
        'type' => 'Resource',
        'icon' => 'bi-box-seam text-info',
        'title' => $row['resource_name'],
        'desc' => $row['category'] . ' — ' . $row['status'],
        'url' => 'resources.php'
    ];
}

// 2. Search Bookings
$stmt2 = $conn->prepare("SELECT id, resource_name, booked_by, booking_date FROM bookings WHERE resource_name LIKE ? OR booked_by LIKE ? LIMIT 3");
$stmt2->bind_param("ss", $search, $search);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($row = $res2->fetch_assoc()) {
    $results[] = [
        'type' => 'Booking',
        'icon' => 'bi-calendar2-check-fill text-success',
        'title' => $row['resource_name'] . ' (by ' . $row['booked_by'] . ')',
        'desc' => 'Date: ' . date('d M Y', strtotime($row['booking_date'])),
        'url' => 'bookings.php'
    ];
}

// 3. Search Departments
$stmt3 = $conn->prepare("SELECT department_name, hod_name FROM departments WHERE department_name LIKE ? OR hod_name LIKE ? LIMIT 3");
$stmt3->bind_param("ss", $search, $search);
$stmt3->execute();
$res3 = $stmt3->get_result();
while ($row = $res3->fetch_assoc()) {
    $results[] = [
        'type' => 'Department',
        'icon' => 'bi-building text-warning',
        'title' => $row['department_name'],
        'desc' => 'HOD: ' . $row['hod_name'],
        'url' => 'departments.php'
    ];
}

echo json_encode($results);
?>
