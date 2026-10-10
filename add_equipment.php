<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $equipment_name = $_POST["equipment_name"];
    $category = $_POST["category"];
    $quantity = intval($_POST["quantity"]);
    $location = $_POST["location"];
    $status = $_POST["status"];
    $description = $_POST["description"];

    $image_name = "";

    // Upload folder
    $upload_dir = "uploads/equipment/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Photo upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

        $allowed_types = ["jpg", "jpeg", "png", "webp"];

        $file_name = $_FILES["image"]["name"];
        $file_tmp = $_FILES["image"]["tmp_name"];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if (in_array($extension, $allowed_types)) {

            $image_name = time() . "_" . basename($file_name);

            move_uploaded_file(
                $file_tmp,
                $upload_dir . $image_name
            );
        }
    }

    // Insert into database
    $stmt = $conn->prepare(
        "INSERT INTO equipment
        (equipment_name, category, quantity, location, status, description, image)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssissss",
        $equipment_name,
        $category,
        $quantity,
        $location,
        $status,
        $description,
        $image_name
    );

    if ($stmt->execute()) {

        header("Location: equipment.php?msg=Equipment+added+successfully");
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
    <title>Add Equipment</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
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
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #125ca1;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #1976d2;
        }

        .message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <h1>âž• Add Equipment</h1>

    <?php if (!empty($message)) { ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Equipment Name</label>
        <input
            type="text"
            name="equipment_name"
            placeholder="Example: Projector"
            required
        >

        <label>Category</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Computer">Computer</option>
            <option value="Projector">Projector</option>
            <option value="Printer">Printer</option>
            <option value="UPS">UPS</option>
            <option value="Speaker">Speaker</option>
            <option value="Other">Other</option>
        </select>

        <label>Quantity</label>
        <input
            type="number"
            name="quantity"
            min="1"
            placeholder="Example: 5"
            required
        >

        <label>Location</label>
        <input
            type="text"
            name="location"
            placeholder="Example: Computer Lab 1"
            required
        >

        <label>Status</label>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="In Use">In Use</option>
            <option value="Under Maintenance">Under Maintenance</option>
            <option value="Not Available">Not Available</option>
        </select>

        <label>Description</label>
        <textarea
            name="description"
            placeholder="Enter equipment details"
        ></textarea>

        <label>Equipment Photo</label>
        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <button type="submit">
            Add Equipment
        </button>

    </form>

    <a class="back" href="equipment.php">
        â† Back to Equipment
    </a>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>



