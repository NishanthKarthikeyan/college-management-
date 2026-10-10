<?php
include('db.php'); 

$resources = [];
$selected_type = $_POST['resource_type'] ?? 'classrooms';
$check_date = $_POST['check_date'] ?? '';

if ($check_date) {
    // Dynamically select table based on user choice
    $table = "classrooms";
    if ($selected_type == 'labs') $table = "computer_labs"; 
    elseif ($selected_type == 'meeting_rooms') $table = "meeting_rooms";
    elseif ($selected_type == 'seminar_halls') $table = "seminar_halls";

    $query = "SELECT * FROM $table";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $resources[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Resource Availability</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 650px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); margin: auto; }
        h2, h3 { color: #333; }
        form { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; margin-top: 10px; }
        input[type="date"], select { padding: 8px; width: 100%; max-width: 250px; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 9px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        ul { padding-left: 20px; }
        li { margin-bottom: 10px; font-size: 16px; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>
<body>
    <div class="container">
        <h2>Find Available Resources</h2>
        <form method="POST" action="">
            <label>Select Resource Type:</label>
            <select name="resource_type">
                <option value="classrooms" <?php if($selected_type=='classrooms') echo 'selected'; ?>>Classrooms</option>
                <option value="labs" <?php if($selected_type=='labs') echo 'selected'; ?>>Computer Labs</option>
                <option value="meeting_rooms" <?php if($selected_type=='meeting_rooms') echo 'selected'; ?>>Meeting Rooms</option>
                <option value="seminar_halls" <?php if($selected_type=='seminar_halls') echo 'selected'; ?>>Seminar Halls</option>
            </select>

            <label>Select Date:</label>
            <input type="date" name="check_date" value="<?php echo htmlspecialchars($check_date); ?>" required>
            
            <br>
            <button type="submit" name="submit">Check Availability</button>
        </form>

        <?php if ($check_date): ?>
            <h3>List for <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $selected_type))); ?> on: <?php echo htmlspecialchars($check_date); ?></h3>
            <?php if (count($resources) > 0): ?>
                <ul>
                    <?php foreach ($resources as $res): ?>
                        <?php 
                            // Checking multiple possible column names for class/room name
                            $room_name = $res['name'] ?? $res['Name'] ?? $res['room_name'] ?? $res['class_name'] ?? $res['title'] ?? 'Unnamed';
                            $department = $res['department'] ?? $res['Department'] ?? 'General';
                            $capacity = $res['capacity'] ?? $res['Capacity'] ?? 'N/A';
                        ?>
                        <li>
                            <strong><?php echo htmlspecialchars($room_name); ?></strong> 
                            <span style="color: #666; font-size: 14px;">
                                (<?php echo htmlspecialchars($department); ?> - Capacity: <?php echo htmlspecialchars($capacity); ?>)
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No resources found.</p>
            <?php endif; ?>
        <?php endif; ?>

        <br>
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    </div>
<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>


