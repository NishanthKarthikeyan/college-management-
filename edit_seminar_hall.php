<?php
include "db.php";

if (!isset($_GET["id"])) {
    die("Seminar Hall ID not found.");
}

$id = intval($_GET["id"]);

$stmt = $conn->prepare("SELECT * FROM seminar_halls WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Seminar Hall not found.");
}

$hall = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hall_name = $_POST["hall_name"];
    $block_name = $_POST["block_name"];
    $room_number = $_POST["room_number"];
    $capacity = intval($_POST["capacity"]);
    $status = $_POST["status"];
    $facilities = $_POST["facilities"];

    $image_name = $hall["image"];

    if (!empty($_FILES["image"]["name"])) {

        $upload_dir = "uploads/seminar_halls/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target = $upload_dir . $image_name;

        move_uploaded_file($_FILES["image"]["tmp_name"], $target);
    }

    $stmt = $conn->prepare(
        "UPDATE seminar_halls 
         SET hall_name=?, block_name=?, room_number=?, capacity=?, 
             status=?, facilities=?, image=? 
         WHERE id=?"
    );

    $stmt->bind_param(
        "sssisssi",
        $hall_name,
        $block_name,
        $room_number,
        $capacity,
        $status,
        $facilities,
        $image_name,
        $id
    );

    if ($stmt->execute()) {
        header("Location: seminar_halls.php?msg=Seminar+Hall+updated+successfully");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Seminar Hall</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
        }
    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <a href="seminar_halls.php" class="back">
        â† Back to Seminar Halls
    </a>

    <h1>ðŸŽ¤ Edit Seminar Hall</h1>

    <form method="POST" enctype="multipart/form-data">

        <label>Hall Name</label>
        <input type="text" name="hall_name"
               value="<?php echo htmlspecialchars($hall["hall_name"]); ?>"
               required>

        <label>Block Name</label>
        <input type="text" name="block_name"
               value="<?php echo htmlspecialchars($hall["block_name"]); ?>"
               required>

        <label>Room Number</label>
        <input type="text" name="room_number"
               value="<?php echo htmlspecialchars($hall["room_number"]); ?>"
               required>

        <label>Capacity</label>
        <input type="number" name="capacity"
               value="<?php echo htmlspecialchars($hall["capacity"]); ?>"
               required>

        <label>Status</label>
        <select name="status">
            <option value="Available"
                <?php if ($hall["status"] == "Available") echo "selected"; ?>>
                Available
            </option>

            <option value="Occupied"
                <?php if ($hall["status"] == "Occupied") echo "selected"; ?>>
                Occupied
            </option>

            <option value="Maintenance"
                <?php if ($hall["status"] == "Maintenance") echo "selected"; ?>>
                Maintenance
            </option>
        </select>

        <label>Facilities</label>
        <textarea name="facilities"><?php
            echo htmlspecialchars($hall["facilities"]);
        ?></textarea>

        <label>Change Photo</label>
        <input type="file" name="image"
               accept=".jpg,.jpeg,.png,.webp">

        <button type="submit">Update Seminar Hall</button>

    </form>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>


