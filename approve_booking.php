<?php
include "db.php";

if (isset($_GET['id']) && isset($_GET['status'])) {

    $id = intval($_GET['id']);
    $status = $_GET['status'];

    if ($status == "Approved" || $status == "Rejected") {

        $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: bookings.php?msg=Status updated successfully");
exit;
?>

