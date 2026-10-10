<?php
include "db.php";

$students = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()["total"];
$staff = $conn->query("SELECT COUNT(*) AS total FROM staff")->fetch_assoc()["total"];
$classrooms = $conn->query("SELECT COUNT(*) AS total FROM classrooms")->fetch_assoc()["total"];
$labs = $conn->query("SELECT COUNT(*) AS total FROM computer_labs")->fetch_assoc()["total"];
$equipment = $conn->query("SELECT COUNT(*) AS total FROM equipment")->fetch_assoc()["total"];
$meeting_rooms = $conn->query("SELECT COUNT(*) AS total FROM meeting_rooms")->fetch_assoc()["total"];
$seminar_halls = $conn->query("SELECT COUNT(*) AS total FROM seminar_halls")->fetch_assoc()["total"];
$bookings = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()["total"];
$maintenance = $conn->query("SELECT COUNT(*) AS total FROM maintenance")->fetch_assoc()["total"];
$requests = $conn->query("SELECT COUNT(*) AS total FROM resource_requests")->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reports | College RMS</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 30px;
}

.header {
    background: linear-gradient(135deg, #182848, #4b6cb7);
    color: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.header h1 {
    margin: 0 0 8px;
}

.header p {
    margin: 0;
}

.top {
    margin-bottom: 25px;
}

.btn {
    display: inline-block;
    padding: 10px 16px;
    background: #374151;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}

.report-table {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #182848;
    color: white;
    padding: 14px;
    text-align: left;
}

td {
    padding: 14px;
    border-bottom: 1px solid #ddd;
}

tr:hover {
    background: #f8fafc;
}

.number {
    font-weight: bold;
    color: #2563eb;
}

</style>

    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="header">

    <h1>ðŸ“Š Resource Management Reports</h1>

    <p>Overview of college resources and activities</p>

</div>

<div class="top">

    <a href="dashboard.php" class="btn">
        â† Dashboard
    </a>

</div>

<div class="report-table">

<table>

<thead>

<tr>
    <th>S.No</th>
    <th>Resource / Module</th>
    <th>Total Records</th>
</tr>

</thead>

<tbody>

<tr>
    <td>1</td>
    <td>ðŸ‘©â€ðŸŽ“ Students</td>
    <td class="number"><?php echo $students; ?></td>
</tr>

<tr>
    <td>2</td>
    <td>ðŸ‘©â€ðŸ« Staff / Faculty</td>
    <td class="number"><?php echo $staff; ?></td>
</tr>

<tr>
    <td>3</td>
    <td>ðŸ« Classrooms</td>
    <td class="number"><?php echo $classrooms; ?></td>
</tr>

<tr>
    <td>4</td>
    <td>ðŸ’» Computer Labs</td>
    <td class="number"><?php echo $labs; ?></td>
</tr>

<tr>
    <td>5</td>
    <td>ðŸ”§ Equipment</td>
    <td class="number"><?php echo $equipment; ?></td>
</tr>

<tr>
    <td>6</td>
    <td>ðŸ¢ Meeting Rooms</td>
    <td class="number"><?php echo $meeting_rooms; ?></td>
</tr>

<tr>
    <td>7</td>
    <td>ðŸŽ¤ Seminar Halls</td>
    <td class="number"><?php echo $seminar_halls; ?></td>
</tr>

<tr>
    <td>8</td>
    <td>ðŸ“… Bookings</td>
    <td class="number"><?php echo $bookings; ?></td>
</tr>

<tr>
    <td>9</td>
    <td>ðŸ› ï¸ Maintenance</td>
    <td class="number"><?php echo $maintenance; ?></td>
</tr>

<tr>
    <td>10</td>
    <td>ðŸ“¦ Resource Requests</td>
    <td class="number"><?php echo $requests; ?></td>
</tr>

</tbody>

</table>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>


