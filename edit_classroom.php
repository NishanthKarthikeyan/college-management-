<?php
include "db.php";

$message = "";

/* Check ID */
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: classroom.php?msg=Classroom+updated+successfully");
    exit;
}

$id = intval($_GET["id"]);


/* Get Classroom Details */
$stmt = $conn->prepare("SELECT * FROM classrooms WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: classroom.php?msg=Classroom+updated+successfully");
    exit;
}

$classroom = $result->fetch_assoc();

$stmt->close();


/* Update Classroom */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $room_name  = $_POST["room_name"];
    $class_name = $_POST["class_name"];
    $department = $_POST["department"];
    $year       = $_POST["year"];
    $block_name = $_POST["block_name"];
    $capacity   = $_POST["capacity"];
    $status     = $_POST["status"];

    $sql = "UPDATE classrooms SET
            room_name = ?,
            class_name = ?,
            department = ?,
            year = ?,
            block_name = ?,
            capacity = ?,
            status = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    /* 5 strings + 1 integer + 1 string + 1 integer */
    $stmt->bind_param(
        "sssssisi",
        $room_name,
        $class_name,
        $department,
        $year,
        $block_name,
        $capacity,
        $status,
        $id
    );

    if ($stmt->execute()) {

        header("Location: classroom.php?msg=Classroom+updated+successfully");
        exit;

    } else {

        $message = "Update failed: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Classroom | College RMS</title>

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

.card h2 {
    margin-top: 0;
    margin-bottom: 25px;
    color: #182848;
}

.form-group {
    margin-bottom: 18px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-weight: bold;
    color: #333;
}

input,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    background: white;
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

.message {
    background: #ffebee;
    color: #c62828;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.back {
    display: inline-block;
    margin-top: 18px;
    text-decoration: none;
    color: #4b6cb7;
    font-weight: bold;
}

.back:hover {
    color: #182848;
}

</style>

    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="header">

    <h1>ðŸ« Edit Classroom</h1>

    <p>Update classroom and class allocation details</p>

</div>


<div class="container">

<div class="card">

    <h2>Update Classroom Details</h2>


    <?php if ($message != "") { ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>


    <form method="POST">


        <!-- Room Number -->

        <div class="form-group">

            <label>Room Number</label>

            <input
                type="text"
                name="room_name"
                value="<?php echo htmlspecialchars($classroom["room_name"] ?? ""); ?>"
                placeholder="Example: C101"
                required
            >

        </div>


        <!-- Class Name -->

        <div class="form-group">

            <label>Class Name</label>

            <input
                type="text"
                name="class_name"
                value="<?php echo htmlspecialchars($classroom["class_name"] ?? ""); ?>"
                placeholder="Example: B.Sc IT"
                required
            >

        </div>


        <!-- Department -->

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


        <!-- Year -->

        <div class="form-group">

            <label>Year</label>

            <select name="year" required>

                <option value="">Select Year</option>

                <option value="1st Year"
                    <?php if (($classroom["year"] ?? "") == "1st Year") echo "selected"; ?>>
                    1st Year
                </option>

                <option value="2nd Year"
                    <?php if (($classroom["year"] ?? "") == "2nd Year") echo "selected"; ?>>
                    2nd Year
                </option>

                <option value="3rd Year"
                    <?php if (($classroom["year"] ?? "") == "3rd Year") echo "selected"; ?>>
                    3rd Year
                </option>

            </select>

        </div>


        <!-- Block -->

        <div class="form-group">

            <label>Block</label>

            <input
                type="text"
                name="block_name"
                value="<?php echo htmlspecialchars($classroom["block_name"] ?? ""); ?>"
                placeholder="Example: Main Block"
                required
            >

        </div>


        <!-- Capacity -->

        <div class="form-group">

            <label>Capacity</label>

            <input
                type="number"
                name="capacity"
                value="<?php echo htmlspecialchars($classroom["capacity"] ?? ""); ?>"
                placeholder="Example: 60"
                min="1"
                required
            >

        </div>


        <!-- Status -->

        <div class="form-group">

            <label>Status</label>

            <select name="status" required>

                <option value="Available"
                    <?php if (($classroom["status"] ?? "") == "Available") echo "selected"; ?>>
                    Available
                </option>

                <option value="Occupied"
                    <?php if (($classroom["status"] ?? "") == "Occupied") echo "selected"; ?>>
                    Occupied
                </option>

                <option value="Maintenance"
                    <?php if (($classroom["status"] ?? "") == "Maintenance") echo "selected"; ?>>
                    Maintenance
                </option>

            </select>

        </div>


        <button type="submit" class="btn">
            ðŸ’¾ Update Classroom
        </button>

    </form>


    <a href="classroom.php" class="back">
        â† Back to Classroom
    </a>

</div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>

</html>


