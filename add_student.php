<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $department = trim($_POST["department"]);
    $year = trim($_POST["year"]);
    $roll = trim($_POST["roll"]);

    if ($name != "" && $department != "" && $year != "" && $roll != "") {

        $stmt = $conn->prepare(
            "INSERT INTO students (name, department, year, roll)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("ssss", $name, $department, $year, $roll);

        if ($stmt->execute()) {
            header("Location: add_student.php?msg=" . urlencode("Student added successfully!")); exit();
        } else {
            $message = "Error: Student not added.";
        }

        $stmt->close();

    } else {
        $message = "Please fill all details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Student | College RMS</title>

<style>

* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    margin: 0;
    background: #f4f6f9;
}

.header {
    background: #243b55;
    color: white;
    padding: 25px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    margin: 0;
}

.header p {
    margin-top: 5px;
}

.back {
    background: white;
    color: #243b55;
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
}

.container {
    width: 90%;
    max-width: 600px;
    margin: 40px auto;
}

.form-box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
}

.form-box h2 {
    margin-bottom: 8px;
}

.subtitle {
    color: #777;
    margin-bottom: 25px;
}

label {
    display: block;
    margin-top: 15px;
    margin-bottom: 7px;
    font-weight: bold;
}

input,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 7px;
    font-size: 15px;
}

button {
    width: 100%;
    margin-top: 25px;
    padding: 13px;
    border: none;
    border-radius: 8px;
    background: #243b55;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

.message {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 12px;
    border-radius: 7px;
    margin-bottom: 20px;
}

</style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="header">

    <div>
        <h1>ðŸŽ“ Add New Student</h1>
        <p>College Resource Management System</p>
    </div>

    <a href="students.php" class="back">â† Students</a>

</div>


<div class="container">

    <div class="form-box">

        <h2>Student Details</h2>

        <p class="subtitle">
            Add a new student to the database
        </p>


        <?php if ($message != "") { ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <form method="POST">

            <label>Student Name</label>

            <input
                type="text"
                name="name"
                placeholder="Enter student name"
                required
            >


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


            <label>Year</label>

            <select name="year" required>

                <option value="">Select Year</option>

                <option value="1st Year">1st Year</option>

                <option value="2nd Year">2nd Year</option>

                <option value="3rd Year">3rd Year</option>

            </select>


            <label>Roll Number</label>

            <input
                type="text"
                name="roll"
                placeholder="Enter roll number"
                required
            >


            <button type="submit">
                + Add Student
            </button>

        </form>

    </div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>

</html>



