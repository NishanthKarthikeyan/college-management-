<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hall_name = trim($_POST["hall_name"]);
    $block_name = trim($_POST["block_name"]);
    $room_number = trim($_POST["room_number"]);
    $capacity = intval($_POST["capacity"]);
    $status = $_POST["status"];
    $facilities = trim($_POST["facilities"]);

    $image_name = "";

    // Create upload folder if not exists
    $upload_dir = "uploads/seminar_halls/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Photo upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $allowed_types = ["jpg", "jpeg", "png", "webp"];
        $file_name = $_FILES["image"]["name"];
        $file_tmp = $_FILES["image"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {

            $image_name = time() . "_" . basename($file_name);

            move_uploaded_file(
                $file_tmp,
                $upload_dir . $image_name
            );
        }
    }

    $stmt = $conn->prepare(
        "INSERT INTO seminar_halls
        (hall_name, block_name, room_number, capacity, status, facilities, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssisss",
        $hall_name,
        $block_name,
        $room_number,
        $capacity,
        $status,
        $facilities,
        $image_name
    );

    if ($stmt->execute()) {
        header("Location: seminar_halls.php?msg=Seminar+Hall+added+successfully");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Seminar Hall</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #222;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            box-sizing: border-box;
            font-size: 15px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .btn {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <a href="seminar_halls.php" class="back">â† Back to Seminar Halls</a>

    <h1>ðŸŽ¤ Add Seminar Hall</h1>

    <?php if (!empty($message)) { ?>
        <div class="error">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Hall Name</label>
        <input
            type="text"
            name="hall_name"
            placeholder="Example: Seminar Hall 1"
            required
        >

        <label>Block Name</label>
        <input
            type="text"
            name="block_name"
            placeholder="Example: Main Block"
            required
        >

        <label>Room Number</label>
        <input
            type="text"
            name="room_number"
            placeholder="Example: SH-101"
            required
        >

        <label>Capacity</label>
        <input
            type="number"
            name="capacity"
            placeholder="Example: 200"
            min="1"
            required
        >

        <label>Status</label>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="Booked">Booked</option>
            <option value="Maintenance">Maintenance</option>
        </select>

        <label>Facilities</label>
        <textarea
            name="facilities"
            placeholder="Example: Projector, AC, Wi-Fi, Sound System, Stage"
        ></textarea>

        <label>Hall Photo</label>
        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <button type="submit" class="btn">
            + Add Seminar Hall
        </button>

    </form>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>



