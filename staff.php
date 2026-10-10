<?php
include "db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff / Faculty</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h1 {
            text-align: center;
            color: #222;
        }

        .top {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
        }

        .btn {
            background: #2563eb;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
        }

        .staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .staff-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .staff-card h2 {
            margin-top: 0;
            color: #2563eb;
        }

        .staff-card p {
            margin: 10px 0;
            color: #444;
        }

        .empty {
            text-align: center;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }
    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="container">

    <h1>ðŸ‘©â€ðŸ« Staff / Faculty</h1>

    <div class="top">
        <a href="dashboard.php" class="btn">â† Dashboard</a>
        <a href="add_staff.php" class="btn">+ Add New Staff</a>
    </div>

    <div class="staff-grid">

        <?php
        $sql = "SELECT name, department, subject, staff_id FROM staff ORDER BY name";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
        ?>

            <div class="staff-card">

                <h2>
                    <?php echo htmlspecialchars($row['name']); ?>
                </h2>

                <p>
                    <strong>Department:</strong>
                    <?php echo htmlspecialchars($row['department']); ?>
                </p>

                <p>
                    <strong>Subject:</strong>
                    <?php echo htmlspecialchars($row['subject']); ?>
                </p>

                <p>
                    <strong>Staff ID:</strong>
                    <?php echo htmlspecialchars($row['staff_id']); ?>
                </p>

            </div>

        <?php
            }

        } else {
        ?>

            <div class="empty">
                <h3>No Staff Added Yet</h3>
                <p>New staff members can be added using the Add New Staff button.</p>
            </div>

        <?php
        }
        ?>

    </div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>


