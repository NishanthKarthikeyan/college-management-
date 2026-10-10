<?php

include "db.php";

if (!isset($_GET["id"])) {
    die("Equipment ID not found.");
}

$id = intval($_GET["id"]);


// Get existing equipment details
$stmt = $conn->prepare(
    "SELECT * FROM equipment WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Equipment not found.");
}

$equipment = $result->fetch_assoc();

$stmt->close();


// Update equipment
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $equipment_name = $_POST["equipment_name"];
    $category = $_POST["category"];
    $quantity = intval($_POST["quantity"]);
    $location = $_POST["location"];
    $status = $_POST["status"];
    $description = $_POST["description"];

    $image_name = $equipment["image"];


    // Upload folder
    $upload_dir = "uploads/equipment/";

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

            $new_image_name = time() . "_" . basename($file_name);

            if (move_uploaded_file(
                $file_tmp,
                $upload_dir . $new_image_name
            )) {

                // Delete old photo
                if (!empty($equipment["image"])) {

                    $old_image_path =
                        $upload_dir . $equipment["image"];

                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }

                $image_name = $new_image_name;
            }
        }
    }


    // Update database
    $stmt = $conn->prepare(
        "UPDATE equipment SET
        equipment_name = ?,
        category = ?,
        quantity = ?,
        location = ?,
        status = ?,
        description = ?,
        image = ?
        WHERE id = ?"
    );

    $stmt->bind_param(
        "ssissssi",
        $equipment_name,
        $category,
        $quantity,
        $location,
        $status,
        $description,
        $image_name,
        $id
    );


    if ($stmt->execute()) {

        header("Location: equipment.php?msg=Equipment+updated+successfully");
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

    <title>Edit Equipment</title>

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

        .current-image {
            width: 150px;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }

    </style>

    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <h1>âœï¸ Edit Equipment</h1>


    <form method="POST" enctype="multipart/form-data">

        <label>Equipment Name</label>

        <input
            type="text"
            name="equipment_name"
            value="<?php echo htmlspecialchars($equipment["equipment_name"]); ?>"
            required
        >


        <label>Category</label>

        <select name="category" required>

            <option value="">Select Category</option>

            <option value="Computer"
                <?php if ($equipment["category"] == "Computer") echo "selected"; ?>>
                Computer
            </option>

            <option value="Projector"
                <?php if ($equipment["category"] == "Projector") echo "selected"; ?>>
                Projector
            </option>

            <option value="Printer"
                <?php if ($equipment["category"] == "Printer") echo "selected"; ?>>
                Printer
            </option>

            <option value="UPS"
                <?php if ($equipment["category"] == "UPS") echo "selected"; ?>>
                UPS
            </option>

            <option value="Speaker"
                <?php if ($equipment["category"] == "Speaker") echo "selected"; ?>>
                Speaker
            </option>

            <option value="Other"
                <?php if ($equipment["category"] == "Other") echo "selected"; ?>>
                Other
            </option>

        </select>


        <label>Quantity</label>

        <input
            type="number"
            name="quantity"
            min="1"
            value="<?php echo htmlspecialchars($equipment["quantity"]); ?>"
            required
        >


        <label>Location</label>

        <input
            type="text"
            name="location"
            value="<?php echo htmlspecialchars($equipment["location"]); ?>"
            required
        >


        <label>Status</label>

        <select name="status" required>

            <option value="Available"
                <?php if ($equipment["status"] == "Available") echo "selected"; ?>>
                Available
            </option>

            <option value="In Use"
                <?php if ($equipment["status"] == "In Use") echo "selected"; ?>>
                In Use
            </option>

            <option value="Under Maintenance"
                <?php if ($equipment["status"] == "Under Maintenance") echo "selected"; ?>>
                Under Maintenance
            </option>

            <option value="Not Available"
                <?php if ($equipment["status"] == "Not Available") echo "selected"; ?>>
                Not Available
            </option>

        </select>


        <label>Description</label>

        <textarea
            name="description"
            placeholder="Enter equipment details"
        ><?php echo htmlspecialchars($equipment["description"]); ?></textarea>


        <?php if (!empty($equipment["image"])) { ?>

            <label>Current Photo</label>

            <img
                src="uploads/equipment/<?php echo htmlspecialchars($equipment["image"]); ?>"
                class="current-image"
                alt="Equipment Photo"
            >

        <?php } ?>


        <label>Change Photo</label>

        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.webp"
        >


        <button type="submit">
            Update Equipment
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


