<?php

include "db.php";

if (!isset($_GET["id"])) {
    die("Computer Lab ID not found.");
}

$id = intval($_GET["id"]);

// Get existing lab details
$stmt = $conn->prepare("SELECT * FROM computer_labs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Computer Lab not found.");
}

$lab = $result->fetch_assoc();

$stmt->close();


// Update Computer Lab
if (isset($_POST["update_lab"])) {

    $lab_name = $_POST["lab_name"];
    $block_name = $_POST["block_name"];
    $room_number = $_POST["room_number"];
    $department = $_POST["department"];
    $computer_count = $_POST["computer_count"];
    $capacity = $_POST["capacity"];
    $status = $_POST["status"];
    $facilities = $_POST["facilities"];

    $image_name = $lab["image"];

    // New photo upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $upload_dir = "uploads/computer_labs/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $extension = strtolower(
            pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION)
        );

        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($extension, $allowed)) {
            die("Only JPG, JPEG, PNG and WEBP images are allowed.");
        }

        $image_name = uniqid("lab_", true) . "." . $extension;

        $image_path = $upload_dir . $image_name;

        if (!move_uploaded_file(
            $_FILES["image"]["tmp_name"],
            $image_path
        )) {
            die("Photo upload failed.");
        }
    }


    // Update database
    $sql = "UPDATE computer_labs
            SET lab_name = ?,
                block_name = ?,
                room_number = ?,
                department = ?,
                computer_count = ?,
                capacity = ?,
                status = ?,
                facilities = ?,
                image = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssiiissi",
        $lab_name,
        $block_name,
        $room_number,
        $department,
        $computer_count,
        $capacity,
        $status,
        $facilities,
        $image_name,
        $id
    );

    if ($stmt->execute()) {

        header("Location: computer_labs.php?msg=Computer+Lab+updated+successfully");
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

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Computer Lab</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            width: 600px;
            max-width: 90%;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        .back {
            display: inline-block;
            background: #555;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 15px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .current-image {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 25px;
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

    </style>

    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <a href="computer_labs.php" class="back">
        â† Back to Computer Labs
    </a>

    <h1>âœï¸ Edit Computer Lab</h1>

    <div class="form-box">

        <form method="POST" enctype="multipart/form-data">

            <label>Lab Name</label>

            <input
                type="text"
                name="lab_name"
                value="<?php echo htmlspecialchars($lab['lab_name']); ?>"
                required
            >

            <label>Block</label>

            <input
                type="text"
                name="block_name"
                value="<?php echo htmlspecialchars($lab['block_name']); ?>"
                required
            >

            <label>Room Number</label>

            <input
                type="text"
                name="room_number"
                value="<?php echo htmlspecialchars($lab['room_number']); ?>"
                required
            >

            <label>Department</label>

            <?php
        $dept_res = $conn->query("SELECT dept_name FROM departments ORDER BY dept_name ASC");
        ?>
        <select name="department" required>
            <option value="">Select Department</option>
            <?php while($dept_row = $dept_res->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($dept_row['dept_name']); ?>"><?php echo htmlspecialchars($dept_row['dept_name']); ?></option>
            <?php endwhile; ?>
        </select>

            <label>Number of Computers</label>

            <input
                type="number"
                name="computer_count"
                value="<?php echo htmlspecialchars($lab['computer_count']); ?>"
                min="1"
                required
            >

            <label>Capacity</label>

            <input
                type="number"
                name="capacity"
                value="<?php echo htmlspecialchars($lab['capacity']); ?>"
                min="1"
                required
            >

            <label>Status</label>

            <select name="status" required>

                <option value="Available"
                    <?php if ($lab['status'] == "Available") echo "selected"; ?>>
                    Available
                </option>

                <option value="In Use"
                    <?php if ($lab['status'] == "In Use") echo "selected"; ?>>
                    In Use
                </option>

                <option value="Maintenance"
                    <?php if ($lab['status'] == "Maintenance") echo "selected"; ?>>
                    Maintenance
                </option>

            </select>

            <label>Facilities</label>

            <textarea name="facilities"><?php
                echo htmlspecialchars($lab['facilities']);
            ?></textarea>

            <label>Current Photo</label>

            <?php if (!empty($lab['image'])) { ?>

                <img
                    src="uploads/computer_labs/<?php echo htmlspecialchars($lab['image']); ?>"
                    class="current-image"
                >

            <?php } else { ?>

                <p>No photo uploaded.</p>

            <?php } ?>

            <label>Change Photo</label>

            <input
                type="file"
                name="image"
                accept=".jpg,.jpeg,.png,.webp"
            >

            <button type="submit" name="update_lab">
                ðŸ’¾ Update Computer Lab
            </button>

        </form>

    </div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>

</html>


