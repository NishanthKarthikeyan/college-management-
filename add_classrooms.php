<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $room_name = $_POST["room_name"];
    $class_name = $_POST["class_name"];
    $department = $_POST["department"];
    $year = $_POST["year"];
    $block_name = $_POST["block_name"];
    $capacity = $_POST["capacity"];
    $status = $_POST["status"];

    $sql = "INSERT INTO classrooms
            (room_name, class_name, department, year, block_name, capacity, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssis",
        $room_name,
        $class_name,
        $department,
        $year,
        $block_name,
        $capacity,
        $status
    );

    if ($stmt->execute()) {
        header("Location: classroom.php?msg=Classroom+added+successfully"); exit();
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Classroom | College RMS</title>

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
    margin: 6px 0 0;
    opacity: 0.9;
}

.container {
    width: 90%;
    max-width: 700px;
    margin: 35px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

h2 {
    margin-top: 0;
    color: #182848;
}

.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
    color: #333;
}

input,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
}

input:focus,
select:focus {
    outline: none;
    border-color: #4b6cb7;
}

.btn {
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 8px;
    background: #4b6cb7;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

.btn:hover {
    background: #182848;
}

.back {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #4b6cb7;
    font-weight: bold;
}

.message {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="header">
    <h1>ðŸ« Add Classroom</h1>
    <p>Add classroom and class allocation details</p>
</div>

<div class="container">

<div class="card">

<h2>Classroom Details</h2>



<form method="POST">

    <div class="form-group">
        <label>Room Number</label>
        <input type="text" name="room_name"
               placeholder="Example: C101" required>
    </div>

    <div class="form-group">
        <label>Class Name</label>
        <input type="text" name="class_name"
               placeholder="Example: 1st BCA" required>
    </div>

    <div class="form-group">
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
    </div>

    <div class="form-group">
        <label>Year</label>
        <select name="year" required>
            <option value="">Select Year</option>
            <option value="1st Year">1st Year</option>
            <option value="2nd Year">2nd Year</option>
            <option value="3rd Year">3rd Year</option>
        </select>
    </div>

    <div class="form-group">
        <label>Block</label>
        <input type="text" name="block_name"
               placeholder="Example: Main Block" required>
    </div>

    <div class="form-group">
        <label>Capacity</label>
        <input type="number" name="capacity"
               placeholder="Example: 60" min="1" required>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="Occupied">Occupied</option>
            <option value="Maintenance">Maintenance</option>
        </select>
    </div>

    <button type="submit" class="btn">
        âž• Add Classroom
    </button>

</form>

<a href="classrooms.php" class="back">â† Back to Classrooms</a>

</div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>




