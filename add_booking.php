<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $register_number = mysqli_real_escape_string($conn, $_POST['register_number']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $resource_name = mysqli_real_escape_string($conn, $_POST['resource_name']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);

    $sql = "INSERT INTO bookings (student_name, register_number, department, resource_name, booking_date) 
            VALUES ('$student_name', '$register_number', '$department', '$resource_name', '$booking_date')";

    if (mysqli_query($conn, $sql)) {
        header("Location: add_booking.php?msg=" . urlencode("Booking added successfully!")); exit();
    } else {
        $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Resource Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="mobile_view.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Add Resource Booking</h4>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>

                    <form action="add_booking.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Student Name:</label>
                            <input type="text" name="student_name" class="form-control" placeholder="Enter full name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Register Number:</label>
                            <input type="text" name="register_number" class="form-control" placeholder="e.g. 21CS001" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Department:</label>
                            <select name="department" class="form-select" required>
                                <option value="">-- Select Department --</option>
                                <?php
                                $departments = [
                                    'placement',
                                    'management',
                                    'Commerce',
                                    'Computer Science',
                                    'Physics',
                                    'Mathematics',
                                    'English',
                                    'Tamil'
                                ];
                                foreach ($departments as $dept) {
                                    echo "<option value='" . htmlspecialchars($dept) . "'>" . htmlspecialchars($dept) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Resource Name / Facility:</label>
                            <input type="text" name="resource_name" class="form-control" placeholder="e.g. Seminar Hall 1 / Data Center" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Booking Date:</label>
                            <input type="date" name="booking_date" class="form-control" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Submit Booking</button>
                            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>


