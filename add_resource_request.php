<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $resource_name = $_POST['resource_name'];
    $resource_type = $_POST['resource_type'];
    $request_details = $_POST['request_details'];
    $requested_by = $_POST['requested_by'];

    $sql = "INSERT INTO resource_requests 
            (resource_name, resource_type, request_details, requested_by, request_date, status)
            VALUES (?, ?, ?, ?, CURDATE(), 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $resource_name, $resource_type, $request_details, $requested_by);

    if ($stmt->execute()) {
        header("Location: resource_requests.php?msg=Resource+request+submitted+successfully");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Resource Request</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f7fb;
            padding: 40px;
        }

        .box {
            width: 450px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1261d6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="box">

    <h2>âž• Add Resource Request</h2>

    <form method="POST">

        <label>Resource Name</label>
        <input type="text" name="resource_name" required>

        <label>Resource Type</label>
        <select name="resource_type" required>
            <option value="">Select Type</option>
            <option value="Computer Lab">Computer Lab</option>
            <option value="Equipment">Equipment</option>
            <option value="Meeting Room">Meeting Room</option>
            <option value="Seminar Hall">Seminar Hall</option>
            <option value="Classroom">Classroom</option>
        </select>

        <label>Request Details</label>
        <textarea name="request_details" rows="4" required></textarea>

        <label>Requested By</label>
        <input type="text" name="requested_by" required>

        <button type="submit">Submit Request</button>

    </form>

    <a href="resource_requests.php">â† Back to Resource Requests</a>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>



