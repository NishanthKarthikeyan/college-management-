<?php
include "db.php";

$result =$conn->query("SELECT * FROM equipment ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Equipment Management | College RMS</title>
<style>
* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    margin: 0;
    background: #f4f7fb;
}

.header {
    background: linear-gradient(135deg, #182848, #4b6cb7);
    color: white;
    padding: 25px 40px;
}

.header h1 {
    margin: 0;
    font-size: 28px;
}

.header p {
    margin: 7px 0 0;
    opacity: 0.9;
}

.container {
    width: 94%;
    max-width: 1400px;
    margin: 35px auto;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.top-bar h2 {
    margin: 0;
    color: #182848;
}

.add-btn {
    background: #4b6cb7;
    color: white;
    padding: 12px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
}

.add-btn:hover {
    background: #182848;
}

.table-card {
    background: white;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

th {
    background: #182848;
    color: white;
    padding: 14px 12px;
    text-align: left;
}

td {
    padding: 13px 12px;
    border-bottom: 1px solid #eee;
    color: #333;
}

tr:hover {
    background: #f7f9fc;
}

.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}

.status.available {
    background: #e8f5e9;
    color: #2e7d32;
}

.status.maintenance {
    background: #ffebee;
    color: #c62828;
}

.edit-btn {
    background: #e3f2fd;
    color: #1565c0;
    padding: 7px 11px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: bold;
}

.edit-btn:hover {
    background: #1565c0;
    color: white;
}

.delete-btn {
    background: #ffebee;
    color: #c62828;
    padding: 7px 11px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: bold;
}

.delete-btn:hover {
    background: #c62828;
    color: white;
}

.back {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #4b6cb7;
    font-weight: bold;
}

.empty {
    text-align: center;
    padding: 30px;
    color: #777;
}
</style>
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="header">
    <h1>ðŸ› ï¸ Equipment Management</h1>
    <p>Manage college equipment and lab resources</p>
</div>

<div class="container">

    <div class="top-bar">
        <h2>Equipment List</h2>
        <a href="add_equipment.php" class="add-btn">
            + Add Equipment
        </a>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Equipment Name</th>
                    <th>Department</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result &&$result->num_rows > 0) {
                while ($row =$result->fetch_assoc()) {
                    $status = strtolower($row["status"] ?? "available");
                    $status_class = ($status == "available") ? "available" : "maintenance";
            ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($row["id"]); ?>
                    </td>
                    <td>
                        <strong>
                            <?php echo htmlspecialchars($row["equipment_name"] ?? ""); ?>
                        </strong>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row["department"] ?? ""); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row["quantity"] ?? 0); ?>
                    </td>
                    <td>
                        <span class="status <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($row["status"] ?? ""); ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit_equipment.php?id=<?php echo $row["id"]; ?>" class="edit-btn">
                            Edit
                        </a>
                        <a href="delete_equipment.php?id=<?php echo $row["id"]; ?>" class="delete-btn"
                           onclick="return confirm('Are you sure you want to


