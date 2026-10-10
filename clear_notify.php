<?php
include 'db.php';
// Update all unread notifications to 'read'
$conn->query("UPDATE notifications SET status = 'read'");
header("Location: dashboard.php");
exit();
?>

