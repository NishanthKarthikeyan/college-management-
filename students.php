<?php
include "db.php";

/* Add Year column automatically if it does not exist */
$check = $conn->query("SHOW COLUMNS FROM students LIKE 'year'");

if ($check && $check->num_rows == 0) {
    $conn->query("ALTER TABLE students ADD year VARCHAR(20) NOT NULL DEFAULT '1st Year'");
}

/* Search */
$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

/* Get students */
if ($search != "") {

    $search_safe = $conn->real_escape_string($search);

    $sql = "SELECT * FROM students
            WHERE name LIKE '%$search_safe%'
            OR department LIKE '%$search_safe%'
            OR roll LIKE '%$search_safe%'
            ORDER BY id DESC";

} else {

    $sql = "SELECT * FROM students ORDER BY id DESC";
}

$result = $conn->query($sql);

/* Count students */
$count_result = $conn->query("SELECT COUNT(*) AS total FROM students");
$count_row = $count_result->fetch_assoc();
$total_students = $count_row['total'];

/* Year counts */
$first_result = $conn->query("SELECT COUNT(*) AS total FROM students WHERE year='1st Year'");
$first_row = $first_result->fetch_assoc();

$second_result = $conn->query("SELECT COUNT(*) AS total FROM students WHERE year='2nd Year'");
$second_row = $second_result->fetch_assoc();

$third_result = $conn->query("SELECT COUNT(*) AS total FROM students WHERE year='3rd Year'");
$third_row = $third_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Students | College RMS</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#f4f7fb;
    color:#1e293b;
}

/* HEADER */

.header{
    background:linear-gradient(135deg,#182848,#4b6cb7);
    color:white;
    padding:28px 45px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.header h1{
    font-size:28px;
}

.header p{
    margin-top:7px;
    opacity:.85;
}

.back{
    text-decoration:none;
    color:white;
    background:rgba(255,255,255,.15);
    padding:11px 20px;
    border-radius:10px;
}

/* MAIN */

.container{
    max-width:1200px;
    margin:35px auto;
    padding:0 25px;
}

/* SUMMARY */

.summary{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-bottom:30px;
}

.box{
    background:white;
    padding:22px;
    border-radius:16px;
    box-shadow:0 5px 20px rgba(0,0,0,.07);
}

.box h3{
    color:#64748b;
    font-size:14px;
}

.box h2{
    margin-top:8px;
    font-size:27px;
}

/* SEARCH */

.search-area{
    background:white;
    padding:20px;
    border-radius:16px;
    display:flex;
    gap:15px;
    margin-bottom:30px;
    box-shadow:0 5px 20px rgba(0,0,0,.07);
}

.search{
    flex:1;
    padding:13px 17px;
    border:1px solid #dbe2ea;
    border-radius:10px;
    outline:none;
    font-size:15px;
}

.search-btn,
.add-btn{
    border:none;
    color:white;
    padding:13px 22px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
    text-decoration:none;
}

.search-btn{
    background:#182848;
}

.add-btn{
    background:#4b6cb7;
}

/* YEAR SECTION */

.year-section{
    background:white;
    padding:28px;
    border-radius:18px;
    margin-bottom:25px;
    box-shadow:0 5px 20px rgba(0,0,0,.07);
}

.year-title{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.year-title h2{
    font-size:22px;
}

.badge{
    background:#e8efff;
    color:#4b6cb7;
    padding:7px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

/* STUDENT CARDS */

.students{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:18px;
}

.student-card{
    border:1px solid #e5eaf1;
    background:#fafcff;
    padding:20px;
    border-radius:14px;
}

.student-icon{
    font-size:30px;
    margin-bottom:10px;
}

.student-card h3{
    font-size:17px;
    margin-bottom:8px;
}

.student-card p{
    color:#64748b;
    font-size:14px;
    margin:5px 0;
}

/* EMPTY */

.empty{
    text-align:center;
    padding:25px;
    color:#64748b;
    border:2px dashed #dbe2ea;
    border-radius:12px;
}

.empty strong{
    display:block;
    color:#334155;
    margin-bottom:5px;
}

/* RESPONSIVE */

@media(max-width:900px){

    .summary{
        grid-template-columns:1fr 1fr;
    }

    .students{
        grid-template-columns:1fr 1fr;
    }

}

@media(max-width:600px){

    .summary,
    .students{
        grid-template-columns:1fr;
    }

    .header{
        padding:22px;
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .search-area{
        flex-direction:column;
    }

}

</style>

    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<!-- HEADER -->

<div class="header">

    <div>
        <h1>ðŸŽ“ Student Management</h1>
        <p>Manage students by department and academic year</p>
    </div>

    <a href="dashboard.php" class="back">
        â† Dashboard
    </a>

</div>


<div class="container">

    <!-- SUMMARY -->

    <div class="summary">

        <div class="box">
            <h3>ðŸ‘©â€ðŸŽ“ Total Students</h3>
            <h2><?php echo $total_students; ?></h2>
        </div>

        <div class="box">
            <h3>1ï¸âƒ£ First Year</h3>
            <h2><?php echo $first_row['total']; ?></h2>
        </div>

        <div class="box">
            <h3>2ï¸âƒ£ Second Year</h3>
            <h2><?php echo $second_row['total']; ?></h2>
        </div>

        <div class="box">
            <h3>3ï¸âƒ£ Third Year</h3>
            <h2><?php echo $third_row['total']; ?></h2>
        </div>

    </div>


    <!-- SEARCH -->

    <form method="GET" class="search-area">

        <input
            type="text"
            name="search"
            class="search"
            placeholder="ðŸ” Search student, roll number or department..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit" class="search-btn">
            Search
        </button>

        <a href="add_student.php" class="add-btn">
            + Add Student
        </a>

    </form>


    <!-- 1ST YEAR -->

    <div class="year-section">

        <div class="year-title">

            <h2>1ï¸âƒ£ 1st Year Students</h2>

            <span class="badge">
                <?php echo $first_row['total']; ?> Students
            </span>

        </div>

        <div class="students">

        <?php

        $found_first = false;

        if ($result && $result->num_rows > 0) {

            while($student = $result->fetch_assoc()) {

                if ($student['year'] == '1st Year') {

                    $found_first = true;

        ?>

            <div class="student-card">

                <div class="student-icon">ðŸ‘¨â€ðŸŽ“</div>

                <h3>
                    <?php echo htmlspecialchars($student['name']); ?>
                </h3>

                <p>
                    ðŸŽ“ <?php echo htmlspecialchars($student['department']); ?>
                </p>

                <p>
                    ðŸ†” Roll No:
                    <?php echo htmlspecialchars($student['roll']); ?>
                </p>

            </div>

        <?php
                }
            }
        }

        if (!$found_first) {
        ?>

            <div class="empty">

                <strong>No 1st Year Students</strong>

                New 1st year students can be added using
                <b>Add Student</b>.

            </div>

        <?php } ?>

        </div>

    </div>


    <!-- 2ND YEAR -->

    <div class="year-section">

        <div class="year-title">

            <h2>2ï¸âƒ£ 2nd Year Students</h2>

            <span class="badge">
                <?php echo $second_row['total']; ?> Students
            </span>

        </div>

        <div class="students">

        <?php

        $found_second = false;

        $result2 = $conn->query($sql);

        if ($result2 && $result2->num_rows > 0) {

            while($student = $result2->fetch_assoc()) {

                if ($student['year'] == '2nd Year') {

                    $found_second = true;

        ?>

            <div class="student-card">

                <div class="student-icon">ðŸ‘¨â€ðŸŽ“</div>

                <h3>
                    <?php echo htmlspecialchars($student['name']); ?>
                </h3>

                <p>
                    ðŸŽ“ <?php echo htmlspecialchars($student['department']); ?>
                </p>

                <p>
                    ðŸ†” Roll No:
                    <?php echo htmlspecialchars($student['roll']); ?>
                </p>

            </div>

        <?php
                }
            }
        }

        if (!$found_second) {
        ?>

            <div class="empty">

                <strong>No 2nd Year Students</strong>

                Space is ready for future students.

            </div>

        <?php } ?>

        </div>

    </div>


    <!-- 3RD YEAR -->

    <div class="year-section">

        <div class="year-title">

            <h2>3ï¸âƒ£ 3rd Year Students</h2>

            <span class="badge">
                <?php echo $third_row['total']; ?> Students
            </span>

        </div>

        <div class="students">

        <?php

        $found_third = false;

        $result3 = $conn->query($sql);

        if ($result3 && $result3->num_rows > 0) {

            while($student = $result3->fetch_assoc()) {

                if ($student['year'] == '3rd Year') {

                    $found_third = true;

        ?>

            <div class="student-card">

                <div class="student-icon">ðŸ‘¨â€ðŸŽ“</div>

                <h3>
                    <?php echo htmlspecialchars($student['name']); ?>
                </h3>

                <p>
                    ðŸŽ“ <?php echo htmlspecialchars($student['department']); ?>
                </p>

                <p>
                    ðŸ†” Roll No:
                    <?php echo htmlspecialchars($student['roll']); ?>
                </p>

            </div>

        <?php
                }
            }
        }

        if (!$found_third) {
        ?>

            <div class="empty">

                <strong>No 3rd Year Students</strong>

                Space is ready for future students.

            </div>

        <?php } ?>

        </div>

    </div>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>

</html>



