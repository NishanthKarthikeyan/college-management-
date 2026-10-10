<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id           = intval($_POST['booking_id']);
    $student_name = $_POST['student_name'];
    $reg_no       = $_POST['reg_no'];
    $department   = $_POST['department'];
    $resource     = $_POST['resource_name'];
    $date         = $_POST['booking_date'];
    $slot         = $_POST['time_slot'];

    // Check if another record already exists with the same date, time, and resource
    $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE resource_name = ? AND booking_date = ? AND (time_slot = ? OR time = ?) AND id != ?");
    $check_stmt->bind_param("ssssi", $resource, $date, $slot, $slot, $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Slot conflict error
        header("Location: bookings.php?status=error&msg=slot_conflict");
        exit();
    }

    // Update query
    $stmt = $conn->prepare("UPDATE bookings SET student_name=?, reg_no=?, department=?, resource_name=?, booking_date=?, time_slot=? WHERE id=?");
    $stmt->bind_param("ssssssi", $student_name, $reg_no, $department, $resource, $date, $slot, $id);
    
    if ($stmt->execute()) {
        header("Location: bookings.php?msg=Booking+updated+successfully");
    } else {
        header("Location: bookings.php?msg=Error+updating+booking");
    }
    exit();
} else {
    header("Location: bookings.php?msg=Booking+updated+successfully");
    exit();
}
?>

