<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Mark all unread notifications as read
$conn->query("UPDATE notifications SET status = 'read' WHERE status = 'unread'");

header("Location: dashboard.php");
exit();
?>
