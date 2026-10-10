<?php
include "db.php";

if (!isset($_GET["id"])) {
    die("Seminar Hall ID not found.");
}

$id = intval($_GET["id"]);

$stmt = $conn->prepare("SELECT image FROM seminar_halls WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Seminar Hall not found.");
}

$hall = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM seminar_halls WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if (!empty($hall["image"])) {
        $image_path = "uploads/seminar_halls/" . $hall["image"];

        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    header("Location: seminar_halls.php?msg=Record deleted successfully");
    exit();

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
?>

