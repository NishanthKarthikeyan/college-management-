<?php

include "db.php";

if (isset($_POST["add_staff"])) {

    $name = $_POST["name"];
    $department = $_POST["department"];
    $subject = $_POST["subject"];
    $staff_id = $_POST["staff_id"];

    $sql = "INSERT INTO staff (name, department, subject, staff_id)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssss",
        $name,
        $department,
        $subject,
        $staff_id
    );

    if ($stmt->execute()) {

        header("Location: staff.php?msg=Staff+added+successfully");
        exit();

    } else {

        echo "Error: " . $stmt->error;

    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Add New Staff</title>

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
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 15px;
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <a href="staff.php" class="back">
        â† Back to Staff
    </a>

    <h1>âž• Add New Staff</h1>

    <div class="form-box">

        <form method="POST">

            <label>Staff Name</label>

            <input
                type="text"
                name="name"
                placeholder="Enter Staff Name"
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


            <label>Subject</label>

            <input
                type="text"
                name="subject"
                placeholder="Enter Subject"
                required
            >


            <label>Staff ID</label>

            <input
                type="text"
                name="staff_id"
                placeholder="Example: STF002"
                required
            >


            <button type="submit" name="add_staff">
                Add Staff
            </button>

        </form>

    </div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>

</html>



