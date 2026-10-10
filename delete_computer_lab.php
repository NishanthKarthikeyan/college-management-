<?php

include "db.php";

if (!isset($_GET["id"])) {
    die("Computer Lab ID not found.");
}

$id = intval($_GET["id"]);

// Get image name before deleting
$stmt = $conn->prepare(
    "SELECT image FROM computer_labs WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Computer Lab not found.");
}

$lab = $result->fetch_assoc();

$stmt->close();


// Delete Computer Lab
$stmt = $conn->prepare(
    "DELETE FROM computer_labs WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    // Delete photo if it exists
    if (!empty($lab["image"])) {

        $image_path = "uploads/computer_labs/" . $lab["image"];

        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    header("Location: computer_labs.php?msg=Record deleted successfully");
    exit();

} else {

    echo "Error: " . $stmt->error;
}

$stmt->close();

?>

