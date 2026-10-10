<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $room_name = $_POST["room_name"];
    $block_name = $_POST["block_name"];
    $room_number = $_POST["room_number"];
    $capacity = intval($_POST["capacity"]);
    $status = $_POST["status"];
    $facilities = $_POST["facilities"];

    $image_name = "";

    // Photo upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $upload_dir = "uploads/meeting_rooms/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $image_path = $upload_dir . $image_name;

        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    // Insert data
    $stmt = $conn->prepare(
        "INSERT INTO meeting_rooms
        (room_name, block_name, room_number, capacity, status, facilities, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssisss",
        $room_name,
        $block_name,
        $room_number,
        $capacity,
        $status,
        $facilities,
        $image_name
    );

    if ($stmt->execute()) {
        header("Location: add_meeting_room.php?msg=" . urlencode("Meeting Room added successfully!")); exit();
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Add Meeting Room</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
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

        .message {
            background: #d1fae5;
            color: #065f46;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            text-align: center;
        }

    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <h1>âž• Add Meeting Room</h1>

    <?php if ($message != "") { ?>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Room Name</label>
        <input type="text" name="room_name" placeholder="Example: Meeting Room 1" required>

        <label>Block Name</label>
        <input type="text" name="block_name" placeholder="Example: Main Block" required>

        <label>Room Number</label>
        <input type="text" name="room_number" placeholder="Example: MR101" required>

        <label>Capacity</label>
        <input type="number" name="capacity" placeholder="Example: 20" min="1" required>

        <label>Status</label>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="Booked">Booked</option>
            <option value="Maintenance">Maintenance</option>
        </select>

        <label>Facilities</label>
        <textarea name="facilities" placeholder="Example: Projector, AC, Wi-Fi"></textarea>

        <label>Room Photo</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">

        <button type="submit">Add Meeting Room</button>

    </form>

    <a class="back" href="meeting_rooms.php">
        â† Back to Meeting Rooms
    </a>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>



