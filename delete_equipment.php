<?php

include "db.php";

if (!isset($_GET["id"])) {
    die("Equipment ID not found.");
}

$id = intval($_GET["id"]);

// Get image before deleting
$stmt = $conn->prepare(
    "SELECT image FROM equipment WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Equipment not found.");
}

$equipment = $result->fetch_assoc();

$stmt->close();


// Delete equipment
$stmt = $conn->prepare(
    "DELETE FROM equipment WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    // Delete photo if exists
    if (!empty($equipment["image"])) {

        $image_path = "uploads/equipment/" . $equipment["image"];

        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    header("Location: equipment.php?msg=Record deleted successfully");
    exit();

} else {

    echo "Error: " . $stmt->error;
}

$stmt->close();

?>

