<?php
include "db.php";

$result = $conn->query("SELECT * FROM seminar_halls ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Seminar Halls | College RMS</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .header {
            background: linear-gradient(135deg, #182848, #4b6cb7);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0 0 8px 0;
        }

        .header p {
            margin: 0;
        }

        .top-buttons {
            margin: 20px 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            margin-right: 8px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
        }

        .dashboard {
            background: #374151;
        }

        .add {
            background: #2563eb;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
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
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f8fafc;
        }

        .hall-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .edit {
            background: #f59e0b;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
        }

        .delete {
            background: #dc2626;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .status {
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
        }

    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="header">

    <h1>ðŸŽ¤ Seminar Hall Management</h1>

    <p>Manage college seminar halls and available facilities</p>

</div>


<div class="top-buttons">

    <a href="dashboard.php" class="btn dashboard">
        â† Dashboard
    </a>

    <a href="add_seminar_hall.php" class="btn add">
        + Add Seminar Hall
    </a>

</div>


<div class="table-container">

<?php if ($result && $result->num_rows > 0) { ?>

<table>

    <thead>

        <tr>
            <th>ID</th>
            <th>Hall Name</th>
            <th>Block</th>
            <th>Room No.</th>
            <th>Capacity</th>
            <th>Status</th>
            <th>Facilities</th>
            <th>Photo</th>
            <th>Action</th>
        </tr>

    </thead>

    <tbody>

    <?php while ($hall = $result->fetch_assoc()) { ?>

        <tr>

            <td>
                <?php echo htmlspecialchars($hall["id"]); ?>
            </td>

            <td>
                <strong>
                    <?php echo htmlspecialchars($hall["hall_name"]); ?>
                </strong>
            </td>

            <td>
                <?php echo htmlspecialchars($hall["block_name"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($hall["room_number"]); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($hall["capacity"]); ?>
            </td>

            <td>
                <span class="status">
                    <?php echo htmlspecialchars($hall["status"]); ?>
                </span>
            </td>

            <td>
                <?php
                if (!empty($hall["facilities"])) {
                    echo htmlspecialchars($hall["facilities"]);
                } else {
                    echo "No facilities";
                }
                ?>
            </td>

            <td>

                <?php if (!empty($hall["image"])) { ?>

                    <img
                        src="uploads/seminar_halls/<?php echo htmlspecialchars($hall["image"]); ?>"
                        class="hall-image"
                        alt="Seminar Hall"
                    >

                <?php } else { ?>

                    No Photo

                <?php } ?>

            </td>

            <td>

                <a
                    href="edit_seminar_hall.php?id=<?php echo $hall["id"]; ?>"
                    class="edit"
                >
                    âœï¸ Edit
                </a>

                <a
                    href="delete_seminar_hall.php?id=<?php echo $hall["id"]; ?>"
                    class="delete"
                    onclick="return confirm('Are you sure you want to delete this Seminar Hall?');"
                >
                    ðŸ—‘ï¸ Delete
                </a>

            </td>

        </tr>

    <?php } ?>

    </tbody>

</table>

<?php } else { ?>

    <div class="no-data">

        <h2>No Seminar Halls Added</h2>

        <p>Click "Add Seminar Hall" to add a seminar hall.</p>

    </div>

<?php } ?>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>


