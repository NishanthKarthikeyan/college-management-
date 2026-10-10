<?php
include "db.php";

if (!isset($_GET["id"])) {
    die("Meeting Room ID not found.");
}

$id = intval($_GET["id"]);

$stmt = $conn->prepare("SELECT image FROM meeting_rooms WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Meeting Room not found.");
}

$room = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM meeting_rooms WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if (!empty($room["image"])) {
        $image_path = "uploads/meeting_rooms/" . $room["image"];

        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    header("Location: meeting_rooms.php?msg=Record deleted successfully");
    exit();

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
?>

