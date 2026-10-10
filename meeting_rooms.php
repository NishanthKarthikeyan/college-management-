<?php

include "db.php";

$search = "";

if (isset($_GET["search"])) {
    $search = $_GET["search"];
}

if ($search != "") {

    $stmt = $conn->prepare(
        "SELECT * FROM meeting_rooms
         WHERE room_name LIKE ?
         OR block_name LIKE ?
         OR room_number LIKE ?
         OR status LIKE ?
         OR facilities LIKE ?
         ORDER BY id DESC"
    );

    $search_value = "%" . $search . "%";

    $stmt->bind_param(
        "sssss",
        $search_value,
        $search_value,
        $search_value,
        $search_value,
        $search_value
    );

    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $result = $conn->query(
        "SELECT * FROM meeting_rooms ORDER BY id DESC"
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meeting Rooms | College RMS</title>

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

        .search-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .search-box form {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .search-box button {
            padding: 11px 20px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        .clear {
            padding: 11px 15px;
            background: #6b7280;
            color: white;
            text-decoration: none;
            border-radius: 6px;
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
            min-width: 1000px;
        }

        th {
            background: #182848;
            color: white;
            padding: 15px;
            text-align: left;
        }

        td {
            padding: 14px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:hover {
            background: #f8fafc;
        }

        .room-name {
            font-weight: bold;
            color: #2563eb;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
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

        <h1>ðŸ¢ Meeting Room Management</h1>

        <p>Manage meeting rooms and available facilities</p>

    </div>


    <div class="top">

        <a href="dashboard.php" class="btn">
            â† Dashboard
        </a>

        <a href="add_meeting_room.php" class="btn">
            + Add Meeting Room
        </a>

    </div>


    <!-- Search -->

    <div class="search-box">

        <form method="GET">

            <input
                type="text"
                name="search"
                placeholder="Search room, block, room number, status..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">
                ðŸ” Search
            </button>

            <?php if ($search != "") { ?>

                <a href="meeting_rooms.php" class="clear">
                    Clear
                </a>

            <?php } ?>

        </form>

    </div>


    <!-- Table -->

    <div class="table-box">

        <?php if ($result && $result->num_rows > 0) { ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Room Name</th>
                        <th>Block</th>
                        <th>Room No.</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Facilities</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php while ($room = $result->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo $room["id"]; ?>
                        </td>

                        <td class="room-name">
                            ðŸ¢ <?php echo htmlspecialchars($room["room_name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($room["block_name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($room["room_number"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($room["capacity"]); ?>
                            Students
                        </td>

                        <td>

                            <?php

                            $status = strtolower($room["status"]);

                            if ($status == "available") {
                                $status_class = "available";
                            } elseif ($status == "booked") {
                                $status_class = "booked";
                            } else {
                                $status_class = "maintenance";
                            }

                            ?>

                            <span class="status <?php echo $status_class; ?>">

                                <?php echo htmlspecialchars($room["status"]); ?>

                            </span>

                        </td>

                        <td>

                            <?php

                            if (!empty($room["facilities"])) {
                                echo htmlspecialchars($room["facilities"]);
                            } else {
                                echo "No facilities";
                            }

                            ?>

                        </td>

                        <td class="actions">

                            <a
                                href="edit_meeting_room.php?id=<?php echo $room["id"]; ?>"
                                class="edit-btn"
                            >
                                âœï¸ Edit
                            </a>

                            <a
                                href="delete_meeting_room.php?id=<?php echo $room["id"]; ?>"
                                class="delete-btn"
                                onclick="return confirm('Are you sure you want to delete this meeting room?');"
                            >
                                ðŸ—‘ï¸ Delete
                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div class="empty">

                <h3>No Meeting Rooms Added Yet</h3>

                <p>Click "Add Meeting Room" to add a new room.</p>

            </div>

        <?php } ?>

    </div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>

</html>


