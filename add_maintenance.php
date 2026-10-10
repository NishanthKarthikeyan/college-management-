<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $resource_name = $_POST["resource_name"];
    $resource_type = $_POST["resource_type"];
    $issue = $_POST["issue"];
    $reported_by = $_POST["reported_by"];
    $report_date = $_POST["report_date"];
    $status = $_POST["status"];

    $stmt = $conn->prepare("INSERT INTO maintenance 
        (resource_name, resource_type, issue, reported_by, report_date, status)
        VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssssss",
        $resource_name,
        $resource_type,
        $issue,
        $reported_by,
        $report_date,
        $status
    );

    if ($stmt->execute()) {
        header("Location: maintenance.php?msg=Maintenance+record+added+successfully");
        exit;
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

    <title>Add Maintenance</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .box {
            width: 500px;
            max-width: 90%;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.15);
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

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="box">

    <h1>ðŸ”§ Add Maintenance</h1>

    <form method="POST">

        <label>Resource Name</label>
        <input type="text" name="resource_name"
               placeholder="Example: Computer Lab 1" required>

        <label>Resource Type</label>
        <select name="resource_type" required>
            <option value="">-- Select Resource Type --</option>
            <option value="Classroom">Classroom</option>
            <option value="Computer Lab">Computer Lab</option>
            <option value="Meeting Room">Meeting Room</option>
            <option value="Seminar Hall">Seminar Hall</option>
            <option value="Equipment">Equipment</option>
        </select>

        <label>Issue</label>
        <textarea name="issue"
                  placeholder="Enter maintenance issue"
                  required></textarea>

        <label>Reported By</label>
        <input type="text" name="reported_by"
               placeholder="Enter your name" required>

        <label>Report Date</label>
        <input type="date" name="report_date" required>

        <label>Status</label>
        <select name="status">
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
        </select>

        <button type="submit">âž• Add Maintenance</button>

    </form>

    <a href="maintenance.php" class="back">â¬… Back to Maintenance</a>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>




