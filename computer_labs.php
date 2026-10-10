<?php

include "db.php";

$sql = "SELECT * FROM computer_labs ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Computer Labs | College RMS</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
            color: #222;
        }

        .container {
            max-width: 1250px;
            margin: auto;
        }

        .header {
            background: linear-gradient(135deg, #182848, #4b6cb7);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .header p {
            margin: 0;
            opacity: 0.9;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn {
            background: #2563eb;
            color: white;
            padding: 11px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .table-box {
            background: white;
            border-radius: 12px;
            overflow-x: auto;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th {
            background: #182848;
            color: white;
            padding: 15px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 14px 15px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8fafc;
        }

        .lab-name {
            font-weight: bold;
            color: #2563eb;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }

        .available {
            background: #dcfce7;
            color: #166534;
        }

        .booked {
            background: #fee2e2;
            color: #991b1b;
        }

        .maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .actions {
            white-space: nowrap;
        }

        .edit-btn,
        .delete-btn {
            display: inline-block;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
            margin-right: 5px;
        }

        .edit-btn {
            background: #f59e0b;
            color: white;
        }

        .delete-btn {
            background: #dc2626;
            color: white;
        }

        .empty {
            text-align: center;
            padding: 40px;
        }

    </style>

    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <div class="header">
        <h1>ðŸ’» Computer Lab Management</h1>
        <p>Manage computer labs and laboratory resources</p>
    </div>

    <div class="top">

        <a href="dashboard.php" class="btn">
            â† Dashboard
        </a>

        <a href="add_computer_lab.php" class="btn">
            + Add Computer Lab
        </a>

    </div>

    <div class="table-box">

        <?php if ($result && $result->num_rows > 0) { ?>

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Lab Name</th>
                    <th>Block</th>
                    <th>Room No.</th>
                    <th>Department</th>
                    <th>Computers</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Facilities</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

            <?php while ($row = $result->fetch_assoc()) { ?>

                <tr>

                    <td>
                        <?php echo $row['id']; ?>
                    </td>

                    <td class="lab-name">
                        <?php echo htmlspecialchars($row['lab_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['block_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['room_number']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['department']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['computer_count']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['capacity']); ?> Students
                    </td>

                    <td>

                        <?php
                        $status = strtolower($row['status']);

                        if ($status == "available") {
                            $status_class = "available";
                        } elseif ($status == "booked") {
                            $status_class = "booked";
                        } else {
                            $status_class = "maintenance";
                        }
                        ?>

                        <span class="status <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>

                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['facilities']); ?>
                    </td>

                    <td class="actions">

                        <a
                            href="edit_computer_lab.php?id=<?php echo $row['id']; ?>"
                            class="edit-btn">
                            âœï¸ Edit
                        </a>

                        <a
                            href="delete_computer_lab.php?id=<?php echo $row['id']; ?>"
                            class="delete-btn"
                            onclick="return confirm('Are you sure you want to delete this computer lab?');">
                            ðŸ—‘ï¸ Delete
                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

        <?php } else { ?>

            <div class="empty">

                <h3>No Computer Labs Added Yet</h3>

                <p>Click "Add Computer Lab" to add a new lab.</p>

            </div>

        <?php } ?>

    </div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>

</html>


