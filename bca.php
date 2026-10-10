<?php
include "db.php";

$years = ["1st Year", "2nd Year", "3rd Year"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>BCA | College RMS</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

body {
    background: #f4f6f9;
    color: #222;
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
    font-size: 28px;
}

.header p {
    margin-top: 5px;
    color: #dbe4ef;
}

.back {
    text-decoration: none;
    background: white;
    color: #243b55;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
}

.container {
    width: 90%;
    max-width: 1100px;
    margin: 30px auto;
}

.title {
    text-align: center;
    margin-bottom: 30px;
}

.title h2 {
    font-size: 26px;
}

.year-box {
    background: white;
    margin-bottom: 25px;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.year-box h3 {
    color: #243b55;
    margin-bottom: 20px;
    font-size: 22px;
}

.students {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 15px;
}

.student-card {
    border: 1px solid #ddd;
    padding: 18px;
    border-radius: 10px;
    background: #fafafa;
}

.student-card h4 {
    margin-bottom: 10px;
    color: #243b55;
}

.student-card p {
    margin: 5px 0;
    color: #555;
}

.empty {
    color: #888;
    background: #f7f7f7;
    padding: 15px;
    border-radius: 8px;
}

.add-btn {
    display: inline-block;
    margin-top: 10px;
    background: #243b55;
    color: white;
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
}

</style>
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="header">

    <div>
        <h1>ðŸŽ“ BCA Department</h1>
        <p>College Resource Management System</p>
    </div>

    <a href="students.php" class="back">â† Students</a>

</div>


<div class="container">

    <div class="title">
        <h2>BCA Students</h2>
        <p>View students year-wise</p>
    </div>


    <?php foreach ($years as $year) { ?>

        <div class="year-box">

            <h3>ðŸ“š <?php echo $year; ?></h3>

            <div class="students">

                <?php

                $stmt = $conn->prepare(
                    "SELECT name, roll 
                     FROM students 
                     WHERE department = 'BCA' AND year = ?
                     ORDER BY name"
                );

                $stmt->bind_param("s", $year);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result->num_rows > 0) {

                    while ($student = $result->fetch_assoc()) {
                ?>

                        <div class="student-card">

                            <h4>ðŸ‘¤ <?php echo htmlspecialchars($student['name']); ?></h4>

                            <p>
                                <strong>Roll No:</strong>
                                <?php echo htmlspecialchars($student['roll']); ?>
                            </p>

                        </div>

                <?php
                    }

                } else {
                ?>

                    <p class="empty">
                        No students added in <?php echo $year; ?> yet.
                    </p>

                <?php
                }

                $stmt->close();
                ?>

            </div>

        </div>

    <?php } ?>


    <a href="add_student.php" class="add-btn">
        + Add New Student
    </a>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>



