<?php
session_start();
include 'db.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKR College Resource Management System</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            padding: 40px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .college-title {
            font-size: 23px;
            line-height: 1.5;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 12px;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            color: white;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 22px;
            }

            .college-title {
                font-size: 20px;
            }
        }
    </style>
    <link rel="stylesheet" href="mobile_view.css">
</head>

<body>

<div class="login-card">

    <div class="text-center mb-4">
        <div class="display-6 text-primary mb-3">
            <i class="bi bi-building-gear"></i>
        </div>

        <h4 class="fw-bold text-dark college-title">
            PKR College Resource Management System
        </h4>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small text-center">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">
                Username
            </label>

            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="bi bi-person"></i>
                </span>

                <input
                    type="text"
                    name="username"
                    class="form-control bg-light"
                    placeholder="admin"
                    required
                >
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-semibold text-secondary">
                Password
            </label>

            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="bi bi-lock"></i>
                </span>

                <input
                    type="password"
                    name="password"
                    class="form-control bg-light"
                    placeholder="Enter password"
                    required
                >
            </div>
        </div>

        <button type="submit" class="btn btn-gradient w-100 mb-3">
            Sign In
        </button>

    </form>

</div>

<script src="voice_notification.js?v=5"></script>
    <script src="mobile_view.js"></script>
</body>
</html>


