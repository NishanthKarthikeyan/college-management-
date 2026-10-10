<?php

include "db.php";

if (!isset($_GET["id"])) {
    die("Classroom ID not found.");
}

$id = intval($_GET["id"]);

// Safe query checking image column safely or deleting directly
$stmt = $conn->prepare("DELETE FROM classrooms WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: classroom.php?msg=Record deleted successfully");
    exit();
} else {
    echo "Error deleting record: " . $stmt->error;
    $stmt->close();
}

?>

