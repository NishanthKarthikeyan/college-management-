<?php

include "db.php";

if (!isset($_GET["id"])) {
    die("Meeting Room ID not found.");
}

$id = intval($_GET["id"]);


// Get existing room details
$stmt = $conn->prepare(
    "SELECT * FROM meeting_rooms WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Meeting Room not found.");
}

$room = $result->fetch_assoc();

$stmt->close();


// Update room
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $room_name = $_POST["room_name"];
    $block_name = $_POST["block_name"];
    $room_number = $_POST["room_number"];
    $capacity = intval($_POST["capacity"]);
    $status = $_POST["status"];
    $facilities = $_POST["facilities"];

    $image_name = $room["image"];

    $upload_dir = "uploads/meeting_rooms/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }


    // New photo upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $allowed_types = ["jpg", "jpeg", "png", "webp"];

        $file_name = $_FILES["image"]["name"];
        $file_tmp = $_FILES["image"]["tmp_name"];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if (in_array($extension, $allowed_types)) {

            $new_image_name =
                time() . "_" . basename($file_name);

            if (move_uploaded_file(
                $file_tmp,
                $upload_dir . $new_image_name
            )) {

                // Delete old image
                if (!empty($room["image"])) {

                    $old_image =
                        $upload_dir . $room["image"];

                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }

                $image_name = $new_image_name;
            }
        }
    }


    // Update database
    $stmt = $conn->prepare(
        "UPDATE meeting_rooms SET
        room_name = ?,
        block_name = ?,
        room_number = ?,
        capacity = ?,
        status = ?,
        facilities = ?,
        image = ?
        WHERE id = ?"
    );

    $stmt->bind_param(
        "sssisssi",
        $room_name,
        $block_name,
        $room_number,
        $capacity,
        $status,
        $facilities,
        $image_name,
        $id
    );


    if ($stmt->execute()) {

        header("Location: meeting_rooms.php?msg=Meeting+Room+updated+successfully");
        exit();

    } else {

        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Meeting Room</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 550px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #222;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px;
            margin-top: 6px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #2563eb;
        }

        .current-image {
            width: 180px;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }

    </style>

    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <h1>âœï¸ Edit Meeting Room</h1>


    <form method="POST" enctype="multipart/form-data">

        <label>Room Name</label>

        <input
            type="text"
            name="room_name"
            value="<?php echo htmlspecialchars($room["room_name"]); ?>"
            required
        >


        <label>Block Name</label>

        <input
            type="text"
            name="block_name"
            value="<?php echo htmlspecialchars($room["block_name"]); ?>"
            required
        >


        <label>Room Number</label>

        <input
            type="text"
            name="room_number"
            value="<?php echo htmlspecialchars($room["room_number"]); ?>"
            required
        >


        <label>Capacity</label>

        <input
            type="number"
            name="capacity"
            min="1"
            value="<?php echo htmlspecialchars($room["capacity"]); ?>"
            required
        >


        <label>Status</label>

        <select name="status" required>

            <option value="Available"
                <?php
                if ($room["status"] == "Available")
                    echo "selected";
                ?>>
                Available
            </option>

            <option value="Booked"
                <?php
                if ($room["status"] == "Booked")
                    echo "selected";
                ?>>
                Booked
            </option>

            <option value="Maintenance"
                <?php
                if ($room["status"] == "Maintenance")
                    echo "selected";
                ?>>
                Maintenance
            </option>

        </select>


        <label>Facilities</label>

        <textarea
            name="facilities"
            placeholder="Example: Projector, AC, Wi-Fi"
        ><?php echo htmlspecialchars($room["facilities"]); ?></textarea>


        <?php if (!empty($room["image"])) { ?>

            <label>Current Photo</label>

            <img
                src="uploads/meeting_rooms/<?php echo htmlspecialchars($room["image"]); ?>"
                class="current-image"
                alt="Meeting Room Photo"
            >

        <?php } ?>


        <label>Change Photo</label>

        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.webp"
        >


        <button type="submit">
            Update Meeting Room
        </button>

    </form>


    <a class="back" href="meeting_rooms.php">
        â† Back to Meeting Rooms
    </a>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>

</html>


