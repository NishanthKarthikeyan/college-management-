<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: bookings.php?msg=Booking+deleted+successfully");
    } else {
        header("Location: bookings.php");
    }
    exit();
} else {
    header("Location: bookings.php?msg=Record deleted successfully");
    exit();
}
?>

